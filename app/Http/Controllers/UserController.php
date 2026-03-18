<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        $name = $user->name;
        $user->delete();
        ActivityLogger::log('deleted', "حذف المستخدم: {$name}");
        return back()->with('success', 'تم حذف المستخدم بنجاح.');
    }
}
