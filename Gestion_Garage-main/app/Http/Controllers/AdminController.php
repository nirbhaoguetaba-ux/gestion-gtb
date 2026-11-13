<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Vehicule;
use App\Models\Mecanique;
use App\Models\Reparation;
use App\Models\Piece;
use App\Models\Facture;
use App\Mail\VehiculePretMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Admin Dashboard with overview KPIs and recent repairs
     */
    public function dashboard()
    {
        $totalVehicules = Vehicule::count();
        $vehiculesEnReparation = Vehicule::whereIn('statut', ['en_reparation', 'en_cours'])->count();
        $vehiculesPrets = Vehicule::whereIn('statut', ['pret', 'prete'])->count();
        $totalTechniciens = User::whereIn('role', ['technicien', 'mecanicien'])->count();
        $totalClients = User::where('role', 'client')->count();
        $chiffreAffaires = Reparation::sum('cout_total');

        $reparationsRecentes = Reparation::with(['vehicule.client.user', 'mecanique.user'])
            ->latest()
            ->take(6)
            ->get();

        $techniciens = User::whereIn('role', ['technicien', 'mecanicien'])
            ->with(['mecanique.reparations' => function($q) {
                $q->whereIn('status', ['en_cours', 'diagnostic', 'en_attente']);
            }])
            ->get();

        $vehicules = Vehicule::with('client.user')
            ->latest()
            ->take(8)
            ->get();

        return view('Admin.dashboard', compact(
            'totalVehicules',
            'vehiculesEnReparation',
            'vehiculesPrets',
            'totalTechniciens',
            'totalClients',
            'chiffreAffaires',
            'reparationsRecentes',
            'techniciens',
            'vehicules'
        ));
    }

    /**
     * Vehicles list with full characteristics and filtering
     */
    public function vehicules(Request $request)
    {
        $query = Vehicule::with(['client.user', 'reparations.mecanique.user']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%");
            });
        }

        if ($request->filled('carburant')) {
            $query->where('fuel_type', $request->input('carburant'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $vehicules = $query->latest()->paginate(12);
        $clients = Client::with('user')->get();

        return view('Admin.vehicules', compact('vehicules', 'clients'));
    }

    /**
     * Store new vehicle with characteristics
     */
    public function storeVehicule(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'annee' => 'required|integer|min:1990|max:2026',
            'fuel_type' => 'required|string',
            'registration_number' => 'required|string|max:25|unique:vehicules,registration_number',
            'vin' => 'nullable|string|max:30',
            'kilometrage' => 'required|integer|min:0',
            'boite_vitesse' => 'required|string',
            'couleur' => 'required|string|max:40',
            'statut' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('vehicules', 'public');
            if ($photoPath === false) {
                return back()->withInput()->withErrors([
                    'photo' => 'La photo du véhicule n’a pas pu être enregistrée. Veuillez réessayer.',
                ]);
            }

            $validated['photo'] = $photoPath;
        }

        Vehicule::create($validated);

        return redirect()->route('admin.vehicules')->with('success', "Le véhicule {$validated['brand']} {$validated['model']} a été enregistré avec succès !");
    }

    /**
     * Show vehicle details with technical specs and repair log
     */
    public function showVehicule($id)
    {
        $vehicule = Vehicule::with(['client.user', 'reparations.mecanique.user'])->findOrFail($id);
        $techniciens = Mecanique::with('user')->get();
        return view('Admin.vehicule_show', compact('vehicule', 'techniciens'));
    }

    /**
     * Update vehicle characteristics
     */
    public function updateVehicule(Request $request, $id)
    {
        $vehicule = Vehicule::findOrFail($id);

        $validated = $request->validate([
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'annee' => 'required|integer',
            'fuel_type' => 'required|string',
            'registration_number' => 'required|string|max:25|unique:vehicules,registration_number,' . $vehicule->id,
            'vin' => 'nullable|string|max:30',
            'kilometrage' => 'required|integer|min:0',
            'boite_vitesse' => 'required|string',
            'couleur' => 'required|string|max:40',
            'statut' => 'required|string',
        ]);

        $vehicule->update($validated);

        return redirect()->back()->with('success', 'Caractéristiques du véhicule mises à jour avec succès.');
    }

    /**
     * Delete vehicle
     */
    public function destroyVehicule($id)
    {
        $vehicule = Vehicule::findOrFail($id);
        $vehicule->delete();
        return redirect()->route('admin.vehicules')->with('success', 'Véhicule supprimé avec succès.');
    }

    /**
     * Repairs list & management
     */
    public function reparations(Request $request)
    {
        $query = Reparation::with(['vehicule.client.user', 'mecanique.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $reparations = $query->latest()->paginate(15);
        $vehicules = Vehicule::all();
        $techniciens = Mecanique::with('user')->get();

        return view('Admin.reparations', compact('reparations', 'vehicules', 'techniciens'));
    }

    /**
     * Create new repair work order
     */
    public function storeReparation(Request $request)
    {
        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'mecanique_id' => 'nullable|exists:mecaniques,id',
            'titre' => 'required|string|max:150',
            'description' => 'nullable|string',
            'priorite' => 'required|string',
            'status' => 'required|string',
            'cout_pieces' => 'nullable|numeric|min:0',
            'cout_main_oeuvre' => 'nullable|numeric|min:0',
        ]);

        $coutPieces = (float) ($validated['cout_pieces'] ?? 0);
        $coutMO = (float) ($validated['cout_main_oeuvre'] ?? 0);
        $validated['cout_total'] = $coutPieces + $coutMO;
        $validated['date_debut'] = now();

        $reparation = Reparation::create($validated);

        // Update vehicle status
        $vehicule = Vehicule::find($validated['vehicule_id']);
        if ($validated['status'] === 'prete' || $validated['status'] === 'pret') {
            $vehicule->update(['statut' => 'pret']);
        } else {
            $vehicule->update(['statut' => 'en_reparation']);
        }

        return redirect()->route('admin.reparations')->with('success', "Ordre de réparation créé avec succès !");
    }

    /**
     * Update repair status and assign technician
     */
    public function updateReparationStatus(Request $request, $id)
    {
        $reparation = Reparation::with(['vehicule.client.user', 'mecanique.user'])->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string',
            'mecanique_id' => 'nullable|exists:mecaniques,id',
            'diagnostic' => 'nullable|string',
            'cout_pieces' => 'nullable|numeric|min:0',
            'cout_main_oeuvre' => 'nullable|numeric|min:0',
        ]);

        if (isset($validated['cout_pieces']) || isset($validated['cout_main_oeuvre'])) {
            $coutPieces = (float) ($validated['cout_pieces'] ?? $reparation->cout_pieces);
            $coutMO = (float) ($validated['cout_main_oeuvre'] ?? $reparation->cout_main_oeuvre);
            $validated['cout_total'] = $coutPieces + $coutMO;
        }

        $newStatus = $validated['status'];

        if ($newStatus === 'prete' || $newStatus === 'pret') {
            $validated['date_fin'] = now();
            if ($reparation->vehicule) {
                $reparation->vehicule->update(['statut' => 'pret']);
            }
        } elseif ($newStatus === 'en_cours' || $newStatus === 'diagnostic') {
            if ($reparation->vehicule) {
                $reparation->vehicule->update(['statut' => 'en_reparation']);
            }
        }

        $reparation->update($validated);

        // Auto trigger notification if marked ready and requested or auto
        if (($newStatus === 'prete' || $newStatus === 'pret') && $request->has('notifier_client')) {
            return $this->sendNotificationEmail($reparation);
        }

        return redirect()->back()->with('success', 'Réparation mise à jour avec succès.');
    }

    /**
     * Trigger email notification to client that vehicle is ready
     */
    public function notifierClient($id)
    {
        $reparation = Reparation::with(['vehicule.client.user', 'mecanique.user'])->findOrFail($id);
        abort_unless(in_array($reparation->status, ['prete', 'pret'], true), 422);

        return $this->sendNotificationEmail($reparation);
    }

    /**
     * Helper to send vehicle ready email
     */
    protected function sendNotificationEmail(Reparation $reparation)
    {
        $client = $reparation->vehicule?->client?->user;
        if (!$client || !$client->email) {
            return redirect()->back()->withErrors([
                'email' => 'Aucune adresse e-mail valide n’est associée au client de cette réparation.',
            ]);
        }

        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            return redirect()->back()->withErrors([
                'email' => 'Le transport d’e-mails n’est pas configuré pour un envoi réel. Renseignez les paramètres SMTP dans le fichier .env.',
            ]);
        }

        try {
            Mail::to($client->email)->send(new VehiculePretMail($reparation));
        } catch (\Throwable $exception) {
            report($exception);
            Log::error('Échec de l’envoi de la notification de réparation.', [
                'reparation_id' => $reparation->id,
                'client_id' => $client->id,
            ]);

            return redirect()->back()->withErrors([
                'email' => 'La réparation est enregistrée, mais l’e-mail n’a pas pu être envoyé. Vérifiez la configuration SMTP puis réessayez.',
            ]);
        }

        $reparation->update([
            'notification_envoyee' => true,
            'notification_envoyee_at' => now(),
            'status' => 'prete',
        ]);
        $reparation->vehicule?->update(['statut' => 'pret']);

        return redirect()->back()->with('success', "L’e-mail d’information a été envoyé à {$client->email}.");
    }

    /**
     * Live in-browser preview of the exact customer email notification
     */
    public function previewMail($id)
    {
        $reparation = Reparation::with(['vehicule.client.user', 'mecanique.user'])->findOrFail($id);
        return view('emails.vehicule_pret', [
            'reparation' => $reparation,
            'vehicule' => $reparation->vehicule,
            'client' => $reparation->vehicule?->client?->user,
            'technicien' => $reparation->mecanique?->user,
        ]);
    }

    /**
     * Technicians directory
     */
    public function techniciens()
    {
        $techniciens = User::whereIn('role', ['technicien', 'mecanicien'])
            ->with(['mecanique.reparations.vehicule'])
            ->get();

        return view('Admin.techniciens', compact('techniciens'));
    }

    /**
     * Store new technician
     */
    public function storeTechnicien(Request $request)
    {
        $validated = $request->validate([
            'Nom' => 'required|string|max:100',
            'Prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'NumeroTelephone' => 'required|string|max:20',
            'specialite' => 'required|string|max:100',
            'statut' => 'required|string',
            'adresse' => 'nullable|string|max:255',
            'password' => 'required|string|min:10',
        ]);

        $user = User::create([
            'Nom' => $validated['Nom'],
            'Prenom' => $validated['Prenom'],
            'username' => strtolower($validated['Prenom'] . '.' . $validated['Nom']),
            'email' => $validated['email'],
            'NumeroTelephone' => $validated['NumeroTelephone'],
            'specialite' => $validated['specialite'],
            'statut' => $validated['statut'],
            'Adresse' => $validated['adresse'] ?? 'Atelier Garage Mécano D-CLIC',
            'role' => 'technicien',
            'password' => Hash::make($validated['password']),
            'avatar' => null,
        ]);

        Mecanique::create([
            'user_id' => $user->id,
            'specialisation' => $validated['specialite'],
        ]);

        return redirect()->route('admin.techniciens')->with('success', "Le technicien {$validated['Prenom']} {$validated['Nom']} a été ajouté avec succès !");
    }

    /**
     * Clients directory
     */
    public function clients()
    {
        $clients = Client::with(['user', 'vehicules.reparations'])->paginate(15);
        return view('Admin.clients', compact('clients'));
    }
}
