<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Validate sort fields
        $allowedSortFields = ['event', 'causer_type', 'subject_type', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Apply filters
        if ($request->filled('event')) {
            $query->where('event', $request->get('event'));
        }

        if ($request->filled('causer_type')) {
            $query->where('causer_type', $request->get('causer_type'));
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->get('subject_type'));
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortDirection);

        // Get paginated results
        $activities = $query->with(['causer', 'subject'])->paginate(20);

        // Get unique values for filters
        $events = Activity::distinct()->pluck('event')->filter()->sort();
        $causerTypes = Activity::distinct()->pluck('causer_type')->filter()->sort();
        $subjectTypes = Activity::distinct()->pluck('subject_type')->filter()->sort();

        return view('activity-logs.index', compact(
            'activities',
            'events',
            'causerTypes',
            'subjectTypes',
            'sortBy',
            'sortDirection'
        ));
    }
}
