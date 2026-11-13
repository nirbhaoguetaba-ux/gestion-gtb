<?php

namespace Database\Seeders;

use App\Models\VehiculeOccasion;
use Illuminate\Database\Seeder;

class VehiculeOccasionSeeder extends Seeder
{
    public function run(): void
    {
        $vehicules = [
            ['VO-2026-001', 'Peugeot', '208 Allure', 2021, 38500, 'Essence', 'Manuelle', 9800000, 'photo-1541899481282-d53bffe3c35d', 'Compacte polyvalente, entretien suivi et carnet disponible.'],
            ['VO-2026-002', 'BMW', '320d Sport', 2020, 61200, 'Diesel', 'Automatique', 18300000, 'photo-1555215695-3004980ad54e', 'Berline confortable, historique d’entretien consultable au garage.'],
            ['VO-2026-003', 'Mercedes-Benz', 'Classe A 200', 2021, 42100, 'Essence', 'Automatique', 17600000, 'photo-1618843479313-40f8afb4b4d8', 'Compacte premium avec équipements de confort et aide à la conduite.'],
            ['VO-2026-004', 'Tesla', 'Model 3 Grande Autonomie', 2022, 33800, 'Électrique', 'Automatique', 23500000, 'photo-1560958089-b8a1929cea89', 'Berline électrique, autonomie et état de batterie vérifiés.'],
            ['VO-2026-005', 'Renault', 'Clio V Intens', 2022, 29400, 'Essence', 'Manuelle', 10800000, 'photo-1549399542-7e3f8b79c341', 'Citadine récente, faible kilométrage et révision à jour.'],
            ['VO-2026-006', 'Audi', 'A4 Avant 35 TDI', 2019, 73400, 'Diesel', 'Automatique', 16300000, 'photo-1606664515524-ed2f786a0bd6', 'Break familial, grand coffre et suivi d’entretien documenté.'],
            ['VO-2026-007', 'Volkswagen', 'Golf 8 Life', 2021, 46800, 'Essence', 'Automatique', 14400000, 'photo-1503376780353-7e6692767b70', 'Compacte polyvalente avec équipements de sécurité complets.'],
            ['VO-2026-008', 'Toyota', 'Yaris Hybride Dynamic', 2022, 31200, 'Hybride', 'Automatique', 13000000, 'photo-1502877338535-766e1452684a', 'Hybride économique, idéale pour les trajets urbains et périurbains.'],
            ['VO-2026-009', 'Dacia', 'Duster Prestige', 2020, 55700, 'Diesel', 'Manuelle', 12100000, 'photo-1502877338535-766e1452684a', 'SUV spacieux, entretien régulier et contrôle technique disponible.'],
            ['VO-2026-010', 'Peugeot', '3008 GT Line', 2021, 49200, 'Diesel', 'Automatique', 17600000, 'photo-1492144534655-ae79c964c9d7', 'SUV familial bien équipé, véhicule contrôlé par notre atelier.'],
        ];

        foreach ($vehicules as [$reference, $marque, $modele, $annee, $kilometrage, $carburant, $boite, $prix, $image, $description]) {
            VehiculeOccasion::updateOrCreate(
                ['reference' => $reference],
                [
                    'marque' => $marque,
                    'modele' => $modele,
                    'annee' => $annee,
                    'kilometrage' => $kilometrage,
                    'carburant' => $carburant,
                    'boite' => $boite,
                    'prix' => $prix,
                    'image_url' => "https://images.unsplash.com/{$image}?auto=format&fit=crop&w=1200&q=85",
                    'description' => $description,
                    'statut' => 'disponible',
                ]
            );
        }
    }
}
