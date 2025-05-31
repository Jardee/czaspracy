<?php

namespace App\Http\Controllers;

use App\Models\WorkEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Validation\Rule; 
use App\Rules\UniqueWorkEntryForUserAndDate;

class WorkEntryController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = WorkEntry::query()->with(['employee', 'enteredBy']);

        if ($user->role === User::ROLE_EMPLOYEE) {
            $query->where('user_id', $user->id);
        } elseif (!Gate::allows('view-all-work-entries')) {
            return redirect('/work-entries')->with('error', 'Nie masz uprawnień do przeglądania tej strony.');
        }

        $employees_for_filter = [];
        if ($user->role === User::ROLE_ADMIN || $user->role === User::ROLE_SUPERVISOR) {
            $employees_for_filter = User::whereIn('role', [User::ROLE_EMPLOYEE, User::ROLE_SUPERVISOR, User::ROLE_ADMIN])
                                        ->orderBy('name')
                                        ->get();

            if ($request->filled('employee_id')) {
                $query->where('user_id', $request->input('employee_id'));
            }
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('date_of_work', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_of_work', '<=', $request->input('date_to'));
        }
        
        $workEntries = $query->latest('date_of_work')->paginate(15)->appends($request->query());

        return view('work_entries.index', compact('workEntries', 'employees_for_filter'));
    }

    public function create()
    {
        if (!Gate::allows('create-work-entry')) {
            return redirect('/work-entries')->with('error', 'Nie masz uprawnień do dodawania wpisów.');
        }

        $employees = User::where('role', User::ROLE_EMPLOYEE)->orderBy('name')->get();
        return view('work_entries.create', compact('employees'));
    }
    
    public function store(Request $request)
    {

        //dd($request->all());
        if (!Gate::allows('create-work-entry')) {
             return redirect('/work-entries')->with('error', 'Nie masz uprawnień do dodawania wpisów.');
        }

    $validatedData = $request->validate([
        'user_id' => 'required|exists:users,id',
        'date_of_work' => [
            'required',
            'date',
            new UniqueWorkEntryForUserAndDate($request->input('user_id'), $request->input('date_of_work'))
        ],
        'hours_worked' => 'required|numeric|min:0.01|max:13',
    ], [
        'hours_worked.max' => 'Liczba godzin nie może przekraczać 13.',
    ]);

        WorkEntry::create([
            'user_id' => $validatedData['user_id'],
            'entered_by_user_id' => Auth::id(),
            'date_of_work' => $validatedData['date_of_work'],
            'hours_worked' => $validatedData['hours_worked'],
        ]);

        return redirect()->route('work-entries.index')->with('success', 'Wpis czasu pracy został dodany.');
    }

    public function show(WorkEntry $workEntry)
    {
        if (!Gate::allows('view-work-entry', $workEntry)) {
            return redirect('/work-entries')->with('error', 'Nie masz uprawnień do przeglądania tego wpisu.');
        }
        $workEntry->load(['employee', 'enteredBy', 'comments.user']);
        return view('work_entries.show', compact('workEntry'));
    }

    public function edit(WorkEntry $workEntry)
    {
        if (!Gate::allows('update-work-entry', $workEntry)) {
            return redirect('/work-entries')->with('error', 'Nie masz uprawnień do edycji tego wpisu.');
        }

        $employees = User::where('role', User::ROLE_EMPLOYEE)->orderBy('name')->get();
        return view('work_entries.edit', compact('workEntry', 'employees'));
    }

    public function update(Request $request, WorkEntry $workEntry)
    {
        if (!Gate::allows('update-work-entry', $workEntry)) {
            return redirect('/work-entries')->with('error', 'Nie masz uprawnień do edycji tego wpisu.');
        }

    $validatedData = $request->validate([
        'user_id' => 'required|exists:users,id',
        'date_of_work' => [
            'required',
            'date',
            new UniqueWorkEntryForUserAndDate($request->input('user_id'), $request->input('date_of_work'), $workEntry->id)
        ],
        'hours_worked' => 'required|numeric|min:0.01|max:13',
    ], [
        'hours_worked.max' => 'Liczba godzin nie może przekraczać 13.',
    ]);

        $workEntry->update($validatedData);

        return redirect()->route('work-entries.index')->with('success', 'Wpis czasu pracy został zaktualizowany.');
    }

    public function destroy(WorkEntry $workEntry)
    {
        if (!Gate::allows('delete-work-entry', $workEntry)) {
            return redirect('/work-entries')->with('error', 'Nie masz uprawnień do usunięcia tego wpisu.');
        }

        $workEntry->delete();

        return redirect()->route('work-entries.index')->with('success', 'Wpis czasu pracy został usunięty.');
    }
}