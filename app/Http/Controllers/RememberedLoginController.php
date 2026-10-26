<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use App\Services\RememberedLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * الدخول بضغطة واحدة من الحسابات المحفوظة على الجهاز، وتبديل الحساب دون خروج.
 */
class RememberedLoginController extends Controller
{
    public function login(Request $request, int $id)
    {
        $user = RememberedLogins::consume($request, $id);

        if (! $user) {
            return redirect()->route('login', ['add' => 1])
                ->withErrors(['email' => 'انتهت صلاحية الحساب المحفوظ، سجّل الدخول بكلمة المرور.']);
        }

        if (Auth::id() === $user->id) {
            return redirect()->route('dashboard');
        }

        // تبديل الحساب: خروج من الحساب الحالي أولاً
        if (Auth::check()) {
            ActivityLogger::log('logout', 'تبديل الحساب إلى: ' . $user->name);
            Auth::logout();
            $request->session()->invalidate();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        ActivityLogger::log('login', 'تسجيل دخول من حساب محفوظ على الجهاز');

        return redirect()->route('dashboard');
    }

    public function forget(Request $request, int $id)
    {
        RememberedLogins::forget($request, $id);

        return back();
    }
}
