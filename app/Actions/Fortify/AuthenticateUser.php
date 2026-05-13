<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticateUser
{
    public function authenticate(Request $request)
    {
        $login = $request->input('login');

        // Détecter si c’est un email ou un pseudo
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'pseudo';

        // Trouver l'utilisateur
        $user = User::where($field, $login)->first();

        // Vérifier le mot de passe
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return null; 
        }

        return $user; 
    }
}

/** ce fichier est ajouté par le codeur 
 * pour permettre la connexion avec le pseudo ou l'email, 
*car le pseudo n'etait pas pris en compte initialement **/