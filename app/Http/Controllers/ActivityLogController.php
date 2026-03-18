<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $action  = $request->get('action');
        $userId  = $request->get('user_id');
        $search  = $request->get('search');
        $date    = $request->get('date');

        $query = ActivityLog::with('user')->latest();

        if ($action) {
            $query->where('action', $action);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('subject_label', 'like', "%{$search}%");
            });
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $logs  = $query->paginate(30)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('activity-logs.index', compact('logs', 'users', 'action', 'userId', 'search', 'date'));
    }
}
