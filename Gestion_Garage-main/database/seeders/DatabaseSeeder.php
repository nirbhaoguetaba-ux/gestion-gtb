<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Vehicule;
use App\Models\Mecanique;
use App\Models\Reparation;
use App\Models\Piece;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demoPassword = 'J0ean@0809';

        // Assurer l'existence des répertoires pour les photos réelles
        $vehiculesDir = public_path('images/vehicules');
        if (!File::exists($vehiculesDir)) {
            File::makeDirectory($vehiculesDir, 0755, true);
        }

        // Téléchargement sécurisé en arrière-plan des vraies photos (avec fallback direct URL)
        $ctx = stream_context_create([
            'http' => ['timeout' => 4, 'user_agent' => 'Mozilla/5.0'],
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
        ]);

        $carPhotos = [
            'peugeot_208.jpg' => 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80',
            'bmw_serie3.jpg' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=800&q=80',
            'mercedes_classe_a.jpg' => 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?auto=format&fit=crop&w=800&q=80',
            'tesla_model3.jpg' => 'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=800&q=80',
            'renault_clio.jpg' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80',
            'audi_a4.jpg' => 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?auto=format&fit=crop&w=800&q=80',
        ];

        foreach ($carPhotos as $file => $url) {
            $dest = public_path('images/vehicules/' . $file);
            if (!File::exists($dest)) {
                try {
                    $content = @file_get_contents($url, false, $ctx);
                    if ($content) {
                        File::put($dest, $content);
                    }
                } catch (\Exception $e) {}
            }
        }

        // 1. Administrateur
        $admin = User::updateOrCreate(
            ['username' => 'admin', 'role' => 'admin'],
            [
                'email' => 'adminguetaba@gmail.com',
                'Nom' => 'GUETABA',
                'Prenom' => 'JACQUES',
                'Adresse' => 'Lomé, Togo',
                'NumeroTelephone' => '+228 90 00 00 01',
                'password' => Hash::make($demoPassword),
                'role' => 'admin',
                'avatar' => null,
            ]
        );

        // 2. Techniciens
        $tech1User = User::updateOrCreate(
            ['username' => 'marc.dupont', 'role' => 'technicien'],
            [
                'email' => 'technicien08@gmail.com',
                'Nom' => 'LUCHA',
                'Prenom' => 'JEAN',
                'Adresse' => 'Lomé, Togo',
                'NumeroTelephone' => '+228 90 00 00 02',
                'password' => Hash::make($demoPassword),
                'role' => 'technicien',
                'specialite' => 'Moteur, Transmission et Freinage',
                'statut' => 'disponible',
                'avatar' => null,
            ]
        );
        $tech1 = Mecanique::updateOrCreate(
            ['user_id' => $tech1User->id],
            ['specialisation' => 'Moteur, Transmission et Freinage']
        );

        $tech2User = User::updateOrCreate(
            ['username' => 'samir.benali', 'role' => 'technicien'],
            [
                'email' => 'technicien08+2@gmail.com',
                'Nom' => 'Benali',
                'Prenom' => 'Samir',
                'Adresse' => 'Lomé, Togo',
                'NumeroTelephone' => '+228 90 00 00 03',
                'password' => Hash::make($demoPassword),
                'role' => 'technicien',
                'specialite' => 'Diagnostic Électronique et Hybride',
                'statut' => 'en_intervention',
                'avatar' => null,
            ]
        );
        $tech2 = Mecanique::updateOrCreate(
            ['user_id' => $tech2User->id],
            ['specialisation' => 'Diagnostic Électronique et Hybride']
        );

        // 3. Clients
        $client1User = User::updateOrCreate(
            ['username' => 'sophie.martin', 'role' => 'client'],
            [
                'email' => 'client02@gmail.com',
                'Nom' => 'LUCHA',
                'Prenom' => 'THEUG',
                'Adresse' => 'Lomé, Togo',
                'NumeroTelephone' => '+228 90 00 00 04',
                'password' => Hash::make($demoPassword),
                'role' => 'client',
                'avatar' => null,
            ]
        );
        $client1 = Client::updateOrCreate(['user_id' => $client1User->id]);

        $client2User = User::updateOrCreate(
            ['username' => 'yao.sivi', 'role' => 'client'],
            [
                'email' => 'client02+2@gmail.com',
                'Nom' => 'SIVI',
                'Prenom' => 'YAO',
                'Adresse' => 'Lomé, Togo',
                'NumeroTelephone' => '+228 90 00 00 05',
                'password' => Hash::make($demoPassword),
                'role' => 'client',
                'avatar' => null,
            ]
        );
        $client2 = Client::updateOrCreate(['user_id' => $client2User->id]);

        // Helper pour assigner la vraie photo (locale si présente, sinon url directe HD)
        $getCarPhoto = function($file, $url) {
            return File::exists(public_path('images/vehicules/' . $file)) ? 'images/vehicules/' . $file : $url;
        };

        // 4. Véhicules avec VRAIES PHOTOS et caractéristiques précises
        $v1 = Vehicule::updateOrCreate(
            ['registration_number' => 'AB-208-GT'],
            [
                'client_id' => $client1->id,
                'brand' => 'Peugeot',
                'model' => '208 GT',
                'annee' => 2022,
                'fuel_type' => 'Essence',
                'vin' => 'VF3CCHNZ6ET102938',
                'kilometrage' => 24500,
                'boite_vitesse' => 'Automatique EAT8',
                'couleur' => 'Bleu Vertigo',
                'statut' => 'pret',
                'photo' => $getCarPhoto('peugeot_208.jpg', $carPhotos['peugeot_208.jpg']),
            ]
        );

        $v2 = Vehicule::updateOrCreate(
            ['registration_number' => 'CD-330-BM'],
            [
                'client_id' => $client1->id,
                'brand' => 'BMW',
                'model' => 'Série 3 Berline',
                'annee' => 2023,
                'fuel_type' => 'Diesel',
                'vin' => 'WBA31AY06PFP54321',
                'kilometrage' => 42100,
                'boite_vitesse' => 'Automatique Steptronic',
                'couleur' => 'Gris Minéral',
                'statut' => 'en_reparation',
                'photo' => $getCarPhoto('bmw_serie3.jpg', $carPhotos['bmw_serie3.jpg']),
            ]
        );

        $v3 = Vehicule::updateOrCreate(
            ['registration_number' => 'EF-180-MB'],
            [
                'client_id' => $client2->id,
                'brand' => 'Mercedes-Benz',
                'model' => 'Classe A 200',
                'annee' => 2021,
                'fuel_type' => 'Essence',
                'vin' => 'WDD1770871J987654',
                'kilometrage' => 38200,
                'boite_vitesse' => 'Automatique 7G-DCT',
                'couleur' => 'Blanc Polaire',
                'statut' => 'en_attente',
                'photo' => $getCarPhoto('mercedes_classe_a.jpg', $carPhotos['mercedes_classe_a.jpg']),
            ]
        );

        $v4 = Vehicule::updateOrCreate(
            ['registration_number' => 'GH-750-TS'],
            [
                'client_id' => $client2->id,
                'brand' => 'Tesla',
                'model' => 'Model 3 Dual Motor',
                'annee' => 2023,
                'fuel_type' => 'Électrique',
                'vin' => '5YJ3E1EB9PF112233',
                'kilometrage' => 19800,
                'boite_vitesse' => 'Automatique',
                'couleur' => 'Rouge Multicouches',
                'statut' => 'en_reparation',
                'photo' => $getCarPhoto('tesla_model3.jpg', $carPhotos['tesla_model3.jpg']),
            ]
        );

        $v5 = Vehicule::updateOrCreate(
            ['registration_number' => 'IJ-555-RN'],
            [
                'client_id' => $client1->id,
                'brand' => 'Renault',
                'model' => 'Clio V TCe',
                'annee' => 2020,
                'fuel_type' => 'Essence',
                'vin' => 'VF1RJA00265432100',
                'kilometrage' => 58000,
                'boite_vitesse' => 'Manuelle 6',
                'couleur' => 'Jaune Éclair',
                'statut' => 'pret',
                'photo' => $getCarPhoto('renault_clio.jpg', $carPhotos['renault_clio.jpg']),
            ]
        );

        $v6 = Vehicule::updateOrCreate(
            ['registration_number' => 'KL-890-AU'],
            [
                'client_id' => $client2->id,
                'brand' => 'Audi',
                'model' => 'A4 Avant TDI',
                'annee' => 2022,
                'fuel_type' => 'Diesel',
                'vin' => 'WAUZZZF42NA109876',
                'kilometrage' => 47600,
                'boite_vitesse' => 'Automatique S-Tronic',
                'couleur' => 'Bleu Navarre',
                'statut' => 'en_reparation',
                'photo' => $getCarPhoto('audi_a4.jpg', $carPhotos['audi_a4.jpg']),
            ]
        );

        // 5. Réparations
        Reparation::updateOrCreate(
            ['vehicule_id' => $v1->id],
            [
                'mecanique_id' => $tech1->id,
                'titre' => 'Révision des 25 000 km et Contrôle Freinage',
                'status' => 'prete',
                'priorite' => 'normale',
                'description' => 'Vidange moteur synthétique 0W20, remplacement du filtre à huile, filtre d\'habitacle, purge du liquide de frein DOT4 et contrôle de géométrie.',
                'diagnostic' => 'Véhicule en parfait état de fonctionnement. Niveaux ajustés, pression des 4 pneumatiques calibrée. Essai routier concluant.',
                'cout_pieces' => 145.50,
                'cout_main_oeuvre' => 120.00,
                'cout_total' => 265.50,
                'notification_envoyee' => true,
                'notification_envoyee_at' => now()->subHours(2),
                'date_debut' => now()->subDays(1),
                'date_fin' => now()->subHours(2),
            ]
        );

        Reparation::updateOrCreate(
            ['vehicule_id' => $v2->id],
            [
                'mecanique_id' => $tech1->id,
                'titre' => 'Remplacement Disques et Plaquettes Avant',
                'status' => 'en_cours',
                'priorite' => 'urgente',
                'description' => 'Témoin d\'usure allumé. Remplacement du train de disques ventilés 348mm et jeu de plaquettes céramiques.',
                'diagnostic' => 'Disques d\'origine sous la cote minimale (28.4mm). Remplacement nécessaire pour la sécurité.',
                'cout_pieces' => 280.00,
                'cout_main_oeuvre' => 160.00,
                'cout_total' => 440.00,
                'notification_envoyee' => false,
                'date_debut' => now()->subHours(5),
            ]
        );

        Reparation::updateOrCreate(
            ['vehicule_id' => $v4->id],
            [
                'mecanique_id' => $tech2->id,
                'titre' => 'Contrôle Système Haute Tension et Géométrie Laser',
                'status' => 'en_cours',
                'priorite' => 'normale',
                'description' => 'Diagnostic logiciel batterie haute tension, vérification du circuit de refroidissement des onduleurs et réglage du train roulant.',
                'diagnostic' => 'Dérive légère constatée vers la droite lors des accélérations. Réalignement du train avant en cours.',
                'cout_pieces' => 75.00,
                'cout_main_oeuvre' => 190.00,
                'cout_total' => 265.00,
                'notification_envoyee' => false,
                'date_debut' => now()->subHours(3),
            ]
        );

        Reparation::updateOrCreate(
            ['vehicule_id' => $v5->id],
            [
                'mecanique_id' => $tech1->id,
                'titre' => 'Changement Kit de Distribution et Pompe à Eau',
                'status' => 'prete',
                'priorite' => 'urgente',
                'description' => 'Échéance des 5 ans atteinte. Remplacement kit courroie crantée, galets tendeurs, pompe à eau et liquide de refroidissement.',
                'diagnostic' => 'Tension de courroie contrôlée aux fréquences prescrites. Aucun jeu dans la poulie damper.',
                'cout_pieces' => 310.00,
                'cout_main_oeuvre' => 280.00,
                'cout_total' => 590.00,
                'notification_envoyee' => true,
                'notification_envoyee_at' => now()->subMinutes(30),
                'date_debut' => now()->subDays(2),
                'date_fin' => now()->subMinutes(30),
            ]
        );

        Reparation::updateOrCreate(
            ['vehicule_id' => $v6->id],
            [
                'mecanique_id' => $tech2->id,
                'titre' => 'Diagnostic Injection et Régénération Filtre à Particules',
                'status' => 'diagnostic',
                'priorite' => 'normale',
                'description' => 'Perte de puissance intermittente en charge. Lecture des codes défauts calculateurs moteur.',
                'diagnostic' => 'Capteur de pression différentielle FAP défaillant (Code P2453). Remplacement effectué.',
                'cout_pieces' => 120.00,
                'cout_main_oeuvre' => 95.00,
                'cout_total' => 215.00,
                'notification_envoyee' => false,
                'date_debut' => now()->subHours(1),
            ]
        );

        // 6. Pièces de rechange
        $pieces = [
            ['nom' => 'Filtre à huile Purflux LS932', 'reference' => 'FLT-001', 'prix' => 14.50, 'quantite' => 35],
            ['nom' => 'Plaquettes de frein avant Brembo', 'reference' => 'BRK-102', 'prix' => 65.00, 'quantite' => 18],
            ['nom' => 'Disques de frein ventilés Bosch 300mm', 'reference' => 'DSC-204', 'prix' => 110.00, 'quantite' => 12],
            ['nom' => 'Huile moteur Castrol Edge 5W30 (5L)', 'reference' => 'OIL-505', 'prix' => 45.90, 'quantite' => 40],
            ['nom' => 'Bougies d\'allumage NGK Iridium', 'reference' => 'SPK-301', 'prix' => 12.00, 'quantite' => 50],
            ['nom' => 'Kit courroie de distribution Gates', 'reference' => 'BLT-402', 'prix' => 180.00, 'quantite' => 8],
            ['nom' => 'Batterie Varta Silver Dynamic 70Ah', 'reference' => 'BAT-701', 'prix' => 135.00, 'quantite' => 15],
        ];

        foreach ($pieces as $p) {
            Piece::updateOrCreate(
                ['reference' => $p['reference']],
                [
                    'nom' => $p['nom'],
                    'prix' => $p['prix'],
                    'quantite' => $p['quantite'],
                    'fournisseur' => 'Fournisseur Central Pièces Auto',
                    'seuil' => 10,
                ]
            );
        }

        $this->call(VehiculeOccasionSeeder::class);
    }
}
