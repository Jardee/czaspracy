<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('manage-users')) {
                abort(403, 'Brak uprawnień do zarządzania użytkownikami.');
            }
            return $next($request);
        });
    }

    public function index(): View
    {
        $users = User::orderBy('name')->paginate(20);
        $roles = [
            User::ROLE_ADMIN => ucfirst(User::ROLE_ADMIN),
            User::ROLE_SUPERVISOR => ucfirst(User::ROLE_SUPERVISOR),
            User::ROLE_EMPLOYEE => ucfirst(User::ROLE_EMPLOYEE),
        ];
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = [
            User::ROLE_ADMIN => ucfirst(User::ROLE_ADMIN),
            User::ROLE_SUPERVISOR => ucfirst(User::ROLE_SUPERVISOR),
            User::ROLE_EMPLOYEE => ucfirst(User::ROLE_EMPLOYEE),
        ];
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_SUPERVISOR, User::ROLE_EMPLOYEE])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został pomyślnie utworzony.');
    }

    public function edit(User $user): View
    {
        $roles = [
            User::ROLE_ADMIN => ucfirst(User::ROLE_ADMIN),
            User::ROLE_SUPERVISOR => ucfirst(User::ROLE_SUPERVISOR),
            User::ROLE_EMPLOYEE => ucfirst(User::ROLE_EMPLOYEE),
        ];
        return view('admin.users.edit', compact('user', 'roles'));
    }
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Nie możesz usunąć samego siebie.');
        }

        if ($user->role === User::ROLE_ADMIN) {
            $adminCount = User::where('role', User::ROLE_ADMIN)->count();
            if ($adminCount <= 1) {
                return redirect()->route('admin.users.index')->with('error', 'Nie można usunąć ostatniego administratora.');
            }
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik ' . $userName . ' został usunięty.');
    }
    public function update(Request $request, User $user): RedirectResponse
    {
        $availableRoles = [User::ROLE_ADMIN, User::ROLE_SUPERVISOR, User::ROLE_EMPLOYEE];

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in($availableRoles)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $dataToUpdate = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }
        
        if (Auth::user()->id === $user->id && $user->role === User::ROLE_ADMIN && $request->role !== User::ROLE_ADMIN) {
            $adminCount = User::where('role', User::ROLE_ADMIN)->where('id', '!=', $user->id)->count();
             if ($adminCount < 1 && User::where('role', User::ROLE_ADMIN)->count() <=1) { 
                return redirect()->route('admin.users.edit', $user)->with('error', 'Nie można odebrać uprawnień ostatniemu administratorowi.');
            }
        }


        $user->update($dataToUpdate);

        return redirect()->route('admin.users.index')->with('success', 'Dane użytkownika ' . $user->name . ' zostały zaktualizowane.');
    }
}