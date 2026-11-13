<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\NouvelleNotification;

class AdminActionService
{
    public function envoyerNotificationAdministrative()
    {
        // Logique pour récupérer l'utilisateur à qui envoyer l'email
        $user = User::find(1);

        // Envoi de la notification
        if ($user) {
            $user->notify(new NouvelleNotification());
        }
    }
}
