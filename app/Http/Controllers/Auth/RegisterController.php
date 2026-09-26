<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'ciudad_origen' => ['nullable', 'string', 'max:255'],
            'consentimiento_marketing' => ['sometimes', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validados['name'],
            'email' => $validados['email'],
            'password' => $validados['password'],
            'ciudad_origen' => $validados['ciudad_origen'] ?? null,
            'consentimiento_marketing' => $request->boolean('consentimiento_marketing'),
        ]);

        $user->assignRole('turista');

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('home');
    }
}