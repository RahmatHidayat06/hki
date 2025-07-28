<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'no_telp' => ['nullable', 'string', 'max:15'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $data = [
            'username' => $validated['username'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_telp' => $validated['no_telp'],
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }
        
        $user->update($data);

        return Redirect::to(route('profile.edit'))
            ->with('status', 'Profile updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
} 