<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vehicule;
use App\Models\Reparation;

class ClientPortalController extends Controller
{
    /**
     * Client Portal Dashboard: vehicles and live repair trackers
     */
    public function dashboard()
    {
        $user = Auth::user();
        $client = $user->client;
        $clientId = $client ? $client->id : null;

        $vehicules = Vehicule::with(['reparations.mecanique.user'])
            ->where('client_id', $clientId)
            ->get();

        $vehiculeIds = $vehicules->pluck('id');

        $reparationsEnCours = Reparation::with(['vehicule', 'mecanique.user'])
            ->whereIn('vehicule_id', $vehiculeIds)
            ->whereIn('status', ['en_attente', 'diagnostic', 'en_cours', 'prete'])
            ->latest()
            ->get();

        $historiqueReparations = Reparation::with(['vehicule', 'mecanique.user'])
            ->whereIn('vehicule_id', $vehiculeIds)
            ->where('status', 'terminee')
            ->latest()
            ->get();

        return view('Client.dashboard', compact(
            'user',
            'client',
            'vehicules',
            'reparationsEnCours',
            'historiqueReparations'
        ));
    }
}
