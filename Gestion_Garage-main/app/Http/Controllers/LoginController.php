<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Display login page.
     */
    public function show()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isTechnicien()) {
                return redirect()->route('technicien.dashboard');
            }
            return redirect()->route('client.dashboard');
        }
        return view('auth.login');
    }

    public function inscription()
    {
        return view('auth.inscription');
    }

    /**
     * Handle account registration
     */
    public function postRegistration(Request $request)
    {
        $request->validate([
            'Nom' => ['required', 'string', 'max:255'],
            'Prenom' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'NumeroTelephone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
            'adresse' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'Nom' => $request->Nom,
            'Prenom' => $request->Prenom,
            'username' => $request->username ?? strtolower($request->Prenom . '.' . $request->Nom),
            'email' => $request->email,
            'NumeroTelephone' => $request->NumeroTelephone,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'Adresse' => $request->adresse,
        ]);

        Client::create(['user_id' => $user->id]);

        Auth::login($user);
        return redirect()->route('client.dashboard')->with('success', 'Votre compte a été créé avec succès !');
    }

    /**
     * Handle account login request
     */
    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember_me');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', 'Bienvenue sur votre espace Administrateur');
            }
            if ($user->isTechnicien()) {
                return redirect()->route('technicien.dashboard')->with('success', 'Bienvenue sur votre espace Technicien');
            }
            return redirect()->route('client.dashboard')->with('success', 'Bienvenue sur votre espace Client');
        }

        return redirect()->route('login')->withErrors(['email' => 'Identifiants invalides. Vérifiez votre adresse e-mail ou votre mot de passe.'])->onlyInput('email');
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
