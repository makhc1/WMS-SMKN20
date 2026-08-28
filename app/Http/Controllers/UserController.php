<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->string('search');
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only('search')
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $allowedRoles = ['Staff Picker'];

        if ($user->role === 'Admin') {
            $allowedRoles[] = 'Admin';
            $allowedRoles[] = 'Warehouse Manager';
        } elseif ($user->role === 'Warehouse Manager') {
            $allowedRoles[] = 'Warehouse Manager';
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|in:' . implode(',', $allowedRoles),
            'status' => 'required|string|in:Active,Suspended',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('message', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $currentUser = $request->user();
        $allowedRoles = ['Staff Picker'];

        if ($currentUser->role === 'Admin') {
            $allowedRoles[] = 'Admin';
            $allowedRoles[] = 'Warehouse Manager';
        } elseif ($currentUser->role === 'Warehouse Manager') {
            $allowedRoles[] = 'Warehouse Manager';
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:' . implode(',', $allowedRoles),
            'status' => 'required|string|in:Active,Suspended',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('message', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting oneself
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }
        
        $user->delete();
        return redirect()->route('users.index')->with('message', 'Pengguna berhasil dihapus.');
    }
}
