<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityLogResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:activity-logs.view')->only('index');
    }

    public function index(Request $request): Response
    {
        $query = Activity::query()->with('causer')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('log_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', \App\Support\JalaliDate::toGregorian($request->input('date_from')));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', \App\Support\JalaliDate::toGregorian($request->input('date_to')));
        }

        $logs = $query->paginate(20)->withQueryString();

        return Inertia::render('ActivityLogs/Index', [
            'logs' => ActivityLogResource::collection($logs),
            'filters' => $request->only(['search', 'date_from', 'date_to']),
        ]);
    }
}
