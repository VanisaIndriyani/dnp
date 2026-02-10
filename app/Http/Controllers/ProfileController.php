<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        if ($user->role == 'super_admin') {
            return view('super_admin.profile.edit', compact('user'));
        } elseif ($user->role == 'operator') {
            return view('operator.profile.edit', compact('user'));
        }
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|min:4|max:255|unique:users,nik,' . $user->id,
            'division' => 'nullable|in:case,cover,inner,endplate',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|min:8', // Password optional, confirmed handled by frontend or separate logic if needed, but simple is better here
        ]);

        $data = [
            'name' => $request->name,
            'nik' => $request->nik,
            'division' => $request->division,
        ];

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('photos', 'public');
            $data['photo'] = $path;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
