<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traite la demande de connexion.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Connexion de longue durée (365 jours)
        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Crée un utilisateur administrateur si aucun n'existe.
     * Cette méthode ne devrait être exécutée qu'une seule fois lors de la configuration.
     */
    public function setupAdmin()
    {
        if (User::count() > 0) {
            return redirect()->route('login')
                ->with('warning', 'Un administrateur existe déjà.');
        }

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        return redirect()->route('login')
            ->with('success', 'Utilisateur administrateur créé avec succès. Email: admin@example.com, Mot de passe: password');
    }

    /**
     * Connexion automatique 
     * Cette méthode permet de connecter l'utilisateur admin sans mot de passe
     */
    public function autoLogin()
    {
        // Récupérer le premier utilisateur admin
        $admin = User::first();
        
        if (!$admin) {
            return redirect()->route('setup.admin')
                ->with('warning', 'Aucun utilisateur administrateur n\'existe. Veuillez créer un compte.');
        }
        
        // Connecter automatiquement
        Auth::login($admin, true); // remember=true pour une connexion persistante
        
        return redirect()->route('dashboard')
            ->with('success', 'Connexion automatique réussie.');
    }
}
