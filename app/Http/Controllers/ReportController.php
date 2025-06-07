<?php

namespace App\Http\Controllers;

use App\Models\WorkEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class ReportController extends Controller
{
public function index(Request $request): View
{

    $request->validate([
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date|after_or_equal:date_from',
        'employee_id' => 'nullable|integer|exists:users,id',
    ]);

    $reportData = collect();
    $overallTotalHours = 0;
    $employees_for_filter = collect();
    $user = Auth::user();

    if ($user->role === User::ROLE_ADMIN || $user->role === User::ROLE_SUPERVISOR) {
        $employees_for_filter = User::orderBy('name')->get();
    }

    if ($request->input('action') === 'generate' && $request->filled('date_from') && $request->filled('date_to')) {
        
        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        $query = WorkEntry::query()
            ->join('users', 'work_entries.user_id', '=', 'users.id')
            ->select(
                'users.name as employee_name',
                'work_entries.user_id',
                DB::raw('SUM(hours_worked) as total_hours')
            )
            ->groupBy('work_entries.user_id', 'users.name')
            ->whereBetween('work_entries.date_of_work', [$dateFrom, $dateTo]);

        if ($user->role === User::ROLE_ADMIN || $user->role === User::ROLE_SUPERVISOR) {
            if ($request->filled('employee_id')) {
                $query->where('work_entries.user_id', $request->employee_id);
            }
        } else {
            $query->where('work_entries.user_id', $user->id);
        }


        $reportData = $query->orderBy('users.name')->get();
        $overallTotalHours = $reportData->sum('total_hours');
    }

    return view('reports.index', [
        'reportData' => $reportData,
        'overallTotalHours' => $overallTotalHours,
        'employees_for_filter' => $employees_for_filter,
    ]);
}
}