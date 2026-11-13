<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reparation;
use App\Models\Vehicule;
use App\Mail\VehiculePretMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TechnicienController extends Controller
{
    /**
     * Technician dashboard: assigned jobs and vehicles
     */
    public function dashboard()
    {
        $user = Auth::user();
        $mecanique = $user->mecanique;
        $mecaniqueId = $mecanique ? $mecanique->id : null;

        $reparationsEnCours = Reparation::with(['vehicule.client.user'])
            ->where('mecanique_id', $mecaniqueId)
            ->whereIn('status', ['en_cours', 'diagnostic', 'en_attente'])
            ->get();

        $reparationsPretes = Reparation::with(['vehicule.client.user'])
            ->where('mecanique_id', $mecaniqueId)
            ->whereIn('status', ['prete', 'terminee'])
            ->latest()
            ->take(5)
            ->get();

        $toutesLesReparations = Reparation::with(['vehicule.client.user'])
            ->where('mecanique_id', $mecaniqueId)
            ->latest()
            ->paginate(10);

        return view('Mecanicien.dashboard', compact(
            'user',
            'mecanique',
            'reparationsEnCours',
            'reparationsPretes',
            'toutesLesReparations'
        ));
    }

    /**
     * Update repair status and diagnostic from technician workspace
     */
    public function updateStatus(Request $request, $id)
    {
        $mecanique = Auth::user()->mecanique;
        abort_unless($mecanique, 403);
        $reparation = Reparation::with(['vehicule.client.user', 'mecanique.user'])
            ->where('mecanique_id', $mecanique->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:diagnostic,en_cours,prete',
            'diagnostic' => 'nullable|string',
            'cout_pieces' => 'nullable|numeric|min:0',
            'cout_main_oeuvre' => 'nullable|numeric|min:0',
        ]);

        if (isset($validated['cout_pieces']) || isset($validated['cout_main_oeuvre'])) {
            $coutP = (float) ($validated['cout_pieces'] ?? $reparation->cout_pieces);
            $coutMO = (float) ($validated['cout_main_oeuvre'] ?? $reparation->cout_main_oeuvre);
            $validated['cout_total'] = $coutP + $coutMO;
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

        if ($newStatus === 'prete' && $request->boolean('notifier_client')) {
            return $this->notifyRepairClient($reparation);
        }

        return redirect()->back()->with('success', 'Rapport d\'intervention mis à jour avec succès.');
    }

    public function notifyClient($id)
    {
        $mecanique = Auth::user()->mecanique;
        abort_unless($mecanique, 403);

        $reparation = Reparation::with(['vehicule.client.user', 'mecanique.user'])
            ->where('mecanique_id', $mecanique->id)
            ->whereIn('status', ['prete', 'pret'])
            ->findOrFail($id);

        return $this->notifyRepairClient($reparation);
    }

    private function notifyRepairClient(Reparation $reparation)
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
        ]);

        return redirect()->back()->with('success', "L’e-mail d’information a été envoyé à {$client->email}.");
    }
}
