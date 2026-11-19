<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Http\Requests\ActivityLogFilterRequest;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(ActivityLogFilterRequest $request): View
    {
        $logs = ActivityLog::with('user')
            ->when($request->filled('q'), fn ($q) => $q->where('description', 'like', '%'.$request->q.'%'))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->action))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('activity-logs.index', [
            'logs' => $logs,
            'actions' => ActivityLog::query()->distinct()->orderBy('action')->pluck('action'),
        ]);
    }
}
