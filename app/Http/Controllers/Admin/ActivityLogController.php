<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->byUser((int) $request->user_id);
        }

        if ($request->filled('log_type')) {
            $query->ofType($request->log_type);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        $logs = $query->paginate(25)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $logTypes = ActivityLog::select('log_type')->distinct()->orderBy('log_type')->pluck('log_type');

        return view('admin.pages.activity-log.index', compact('logs', 'users', 'logTypes'));
    }
}
