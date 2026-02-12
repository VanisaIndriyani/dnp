<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $pendingQuery = User::where('status', 'pending');
        $activeQuery = User::where('status', 'active');

        if ($request->has('search')) {
            $search = $request->search;
            $pendingQuery->where('nik', 'like', "%{$search}%");
            $activeQuery->where('nik', 'like', "%{$search}%");
        }

        $pendingUsers = $pendingQuery->latest()->get();
        $users = $activeQuery->latest()->paginate(10);

        return view('super_admin.users.index', compact('users', 'pendingUsers'));
    }

    public function create()
    {
        return view('super_admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|unique:users,nik',
            'role' => 'required|in:super_admin,admin,operator',
            'division' => 'nullable|in:case,cover,inner,endplate',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'nik' => $request->nik,
            'email' => $request->email, // Optional
            'role' => $request->role,
            'division' => $request->division,
            'status' => 'active', // Default active when created by admin
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route(auth()->user()->role . '.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        if (auth()->user()->role == 'super_admin') {
            return view('super_admin.users.edit', compact('user'));
        }
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|unique:users,nik,' . $user->id,
            'role' => 'required|in:super_admin,admin,operator',
            'division' => 'nullable|in:case,cover,inner,endplate',
            'status' => 'required|in:active,pending',
        ]);

        $data = [
            'name' => $request->name,
            'nik' => $request->nik,
            'email' => $request->email,
            'role' => $request->role,
            'division' => $request->division,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route(auth()->user()->role . '.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        
        $user->delete();
        return redirect()->route(auth()->user()->role . '.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', 'User berhasil di-approve.');
    }
}
