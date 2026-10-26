<?php

namespace App\Services;

use App\Models\RememberedLogin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

/**
 * الحسابات المحفوظة على الجهاز: كوكي مشفّرة (httpOnly) تحمل {id: رمز} لكل حساب،
 * وجدول remembered_logins يحمل بصمة الرمز ومدة الصلاحية. الرمز يتجدد عند كل استخدام.
 */
class RememberedLogins
{
    public const COOKIE       = 'remembered_logins';
    public const MAX_ACCOUNTS = 5;
    public const DAYS         = 30;

    /**
     * الحسابات الصالحة على هذا الجهاز، الأحدث استخداماً أولاً.
     */
    public static function forDevice(Request $request): Collection
    {
        $entries = self::entries($request);
        if (empty($entries)) {
            return collect();
        }

        $valid = RememberedLogin::with('user')
            ->whereIn('id', array_keys($entries))
            ->where('expires_at', '>', now())
            ->get()
            ->filter(fn (RememberedLogin $login) => $login->user && self::matches($login, $entries[$login->id]))
            ->sortByDesc('last_used_at')
            ->values();

        // حسابات انتهت أو أُلغيت (تغيير كلمة المرور، حذف المستخدم) تُزال من الكوكي
        if ($valid->count() !== count($entries)) {
            self::queue(array_intersect_key($entries, $valid->keyBy('id')->all()));
        }

        return $valid;
    }

    /**
     * حفظ الحساب على هذا الجهاز بعد دخوله بكلمة المرور.
     */
    public static function remember(Request $request, User $user): void
    {
        $entries = self::entries($request);

        // الحساب نفسه محفوظ سابقاً على هذا الجهاز: يُستبدل
        RememberedLogin::whereIn('id', array_keys($entries))->where('user_id', $user->id)->get()
            ->each(function (RememberedLogin $old) use (&$entries) {
                unset($entries[$old->id]);
                $old->delete();
            });

        $token = Str::random(64);
        $login = RememberedLogin::create([
            'user_id'      => $user->id,
            'token_hash'   => hash('sha256', $token),
            'user_agent'   => Str::limit((string) $request->userAgent(), 250, ''),
            'last_used_at' => now(),
            'expires_at'   => now()->addDays(self::DAYS),
        ]);

        $entries = [$login->id => $token] + $entries;

        // حد أقصى للحسابات على الجهاز الواحد: الأقدم يُحذف
        $overflow = array_slice(array_keys($entries), self::MAX_ACCOUNTS);
        if ($overflow) {
            RememberedLogin::whereIn('id', $overflow)->delete();
            $entries = array_diff_key($entries, array_flip($overflow));
        }

        self::queue($entries);
    }

    /**
     * التحقق من حساب محفوظ وتجديد رمزه — يعيد المستخدم أو null إن لم يكن صالحاً.
     */
    public static function consume(Request $request, int $id): ?User
    {
        $entries = self::entries($request);
        $login   = isset($entries[$id]) ? RememberedLogin::with('user')->find($id) : null;

        if (! $login || ! $login->user || $login->expires_at->isPast() || ! self::matches($login, $entries[$id])) {
            self::forget($request, $id);
            return null;
        }

        $token = Str::random(64);
        $login->update([
            'token_hash'   => hash('sha256', $token),
            'user_agent'   => Str::limit((string) $request->userAgent(), 250, ''),
            'last_used_at' => now(),
            'expires_at'   => now()->addDays(self::DAYS),
        ]);

        self::queue([$id => $token] + $entries);

        return $login->user;
    }

    /**
     * إزالة حساب من هذا الجهاز.
     */
    public static function forget(Request $request, int $id): void
    {
        $entries = self::entries($request);
        if (! isset($entries[$id])) {
            return;
        }

        $login = RememberedLogin::find($id);
        if ($login && self::matches($login, $entries[$id])) {
            $login->delete();
        }

        unset($entries[$id]);
        self::queue($entries);
    }

    /** @return array<int, string> */
    private static function entries(Request $request): array
    {
        $decoded = json_decode((string) $request->cookie(self::COOKIE), true);
        if (! is_array($decoded)) {
            return [];
        }

        $entries = [];
        foreach ($decoded as $id => $token) {
            if (ctype_digit((string) $id) && is_string($token)) {
                $entries[(int) $id] = $token;
            }
        }

        return $entries;
    }

    private static function matches(RememberedLogin $login, string $token): bool
    {
        return hash_equals($login->token_hash, hash('sha256', $token));
    }

    private static function queue(array $entries): void
    {
        if (empty($entries)) {
            Cookie::queue(Cookie::forget(self::COOKIE));
            return;
        }

        // كائن JSON دائماً (لا قائمة) حتى تبقى المفاتيح أرقام الحسابات
        Cookie::queue(self::COOKIE, json_encode((object) $entries), self::DAYS * 24 * 60);
    }
}
