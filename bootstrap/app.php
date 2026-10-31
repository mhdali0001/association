<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminOnly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 419 «انتهت صلاحية الصفحة»: رمز الحماية في النموذج قديم — صفحة تُركت مفتوحة بعد انتهاء الجلسة،
        // أو زر «رجوع» إلى صفحة الدخول بعد دخول نجح ثم تعطلت الصفحة التالية، أو تبديل الحساب من تبويب آخر.
        // بدل صفحة الخطأ نعيد المستخدم إلى نموذج برمز جديد.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419 || $request->expectsJson()) {
                return null;
            }

            Log::info('CSRF token mismatch (419)', [
                'path'           => $request->path(),
                'session_cookie' => $request->hasCookie(config('session.cookie')),
                'user_id'        => Auth::id(),
            ]);

            $message = 'انتهت صلاحية الصفحة، أعد المحاولة.';

            if ($request->is('login', 'login/*')) {
                // محاولة سابقة نجحت فعلاً — لا داعي لإعادة الدخول
                if (Auth::check()) {
                    return redirect()->intended(route('dashboard'));
                }

                return $request->is('login')
                    ? redirect()->route('login')->withInput($request->only('email'))->withErrors(['email' => $message])
                    : redirect()->route('login')->with('error', $message);
            }

            return redirect()->back()
                ->withInput($request->except(['_token', 'password', 'password_confirmation', 'current_password']))
                ->with('error', $message);
        });
    })->create();
