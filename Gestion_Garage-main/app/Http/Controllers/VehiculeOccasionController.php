<?php

namespace App\Http\Controllers;

use App\Models\VehiculeOccasion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VehiculeOccasionController extends Controller
{
    public function index(Request $request)
    {
        $query = VehiculeOccasion::query()->where('statut', 'disponible');

        if ($request->filled('recherche')) {
            $recherche = $request->string('recherche')->trim()->toString();
            $query->where(function ($vehicules) use ($recherche) {
                $vehicules->where('marque', 'like', "%{$recherche}%")
                    ->orWhere('modele', 'like', "%{$recherche}%")
                    ->orWhere('reference', 'like', "%{$recherche}%");
            });
        }

        if ($request->filled('prix_max')) {
            $request->validate(['prix_max' => ['numeric', 'min:0']]);
            $query->where('prix', '<=', $request->input('prix_max'));
        }

        $vehicules = $query->orderBy('marque')->orderBy('modele')->paginate(10)->withQueryString();

        return view('vehicules-occasion.index', compact('vehicules'));
    }

    public function updatePrice(Request $request, VehiculeOccasion $vehiculeOccasion)
    {
        $validated = $request->validate([
            'prix' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($vehiculeOccasion, $validated) {
            $vehicule = VehiculeOccasion::query()
                ->lockForUpdate()
                ->findOrFail($vehiculeOccasion->id);

            if ((int) $validated['prix'] >= (float) $vehicule->prix) {
                throw ValidationException::withMessages([
                    'prix' => 'Le nouveau prix doit être inférieur au prix actuel.',
                ]);
            }

            $vehicule->update(['prix' => $validated['prix']]);
        });

        return redirect()->route('admin.vehicules-occasion')
            ->with('success', 'Le prix du véhicule a été réduit avec succès.');
    }
}
