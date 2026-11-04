<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\RememberedLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // no-store: زر «رجوع» يطلب الصفحة من جديد برمز حماية صالح بدل نسخة قديمة من ذاكرة المتصفح
        return response()
            ->view('auth.login', ['rememberedAccounts' => RememberedLogins::forDevice($request)])
            ->header('Cache-Control', 'no-store, private');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'البريد الإلكتروني مطلوب',
            'email.email'       => 'البريد الإلكتروني غير صالح',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // ما بعد الدخول خدمات إضافية: فشلها (جدول ناقص على الخادم مثلاً) يُسجَّل ولا يُفشل الدخول —
            // وإلا ظهر 500 مع أن الجلسة حُفظت، فينجح الدخول في المحاولة الثانية فقط
            if ($request->boolean('remember')) {
                rescue(fn () => RememberedLogins::remember($request, Auth::user()));
            }
            rescue(fn () => ActivityLogger::log('login', 'تسجيل دخول ناجح'));

            // الدخول يفتح لوحة التحكم دائماً — لا «الصفحة المقصودة» المحفوظة في الجلسة
            $request->session()->forget('url.intended');

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'role'     => ['required', 'in:admin,user'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'name.required'     => 'الاسم مطلوب',
            'email.required'    => 'البريد الإلكتروني مطلوب',
            'email.email'       => 'البريد الإلكتروني غير صالح',
            'email.unique'      => 'البريد الإلكتروني مسجل مسبقاً',
            'role.required'     => 'الصلاحية مطلوبة',
            'role.in'           => 'الصلاحية غير صالحة',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min'      => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        ActivityLogger::log('created', "إضافة مستخدم جديد: {$user->name}");

        return redirect()->route('users.index')->with('success', 'تم إنشاء المستخدم بنجاح.');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
            'password.required'         => 'كلمة المرور الجديدة مطلوبة',
            'password.min'              => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed'        => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        ActivityLogger::log('updated', 'تغيير كلمة المرور');

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }

    public function logout(Request $request)
    {
        ActivityLogger::log('logout', 'تسجيل خروج');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // "إضافة حساب آخر" من قائمة المستخدم: نموذج الدخول مباشرة بدل قائمة الحسابات المحفوظة
        return redirect()->route('login', $request->boolean('add_account') ? ['add' => 1] : []);
    }
}
