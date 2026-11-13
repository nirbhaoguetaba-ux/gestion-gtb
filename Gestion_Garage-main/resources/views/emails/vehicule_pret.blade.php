<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre véhicule est prêt !</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #0f172a;
            color: #334155;
            margin: 0;
            padding: 30px 15px;
        }
        .email-container {
            max-width: 620px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 36px 30px;
            text-align: center;
            color: #ffffff;
        }
        .badge-ready {
            display: inline-block;
            background-color: #10b981;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 6px 16px;
            border-radius: 20px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .header h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
            color: #f8fafc;
        }
        .header p {
            margin: 0;
            color: #94a3b8;
            font-size: 14px;
        }
        .content {
            padding: 32px 30px;
        }
        .greeting {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .vehicle-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            display: flex;
            align-items: center;
        }
        .vehicle-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 6px 0;
        }
        .vehicle-meta {
            font-size: 13px;
            color: #64748b;
            margin: 3px 0;
        }
        .work-box {
            border-left: 4px solid #3b82f6;
            background: #eff6ff;
            padding: 16px 20px;
            border-radius: 0 12px 12px 0;
            margin: 24px 0;
        }
        .work-box h3 {
            margin: 0 0 8px 0;
            font-size: 15px;
            color: #1d4ed8;
        }
        .work-box p {
            margin: 0;
            font-size: 14px;
            color: #1e293b;
            line-height: 1.5;
        }
        .info-grid {
            margin: 25px 0;
            border-collapse: collapse;
            width: 100%;
        }
        .info-grid td {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .info-label {
            color: #64748b;
            width: 45%;
        }
        .info-val {
            font-weight: 600;
            color: #0f172a;
            text-align: right;
        }
        .cta-box {
            text-align: center;
            margin: 30px 0 10px 0;
        }
        .btn-pickup {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            padding: 14px 32px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }
        .garage-info {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px 30px;
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }
        .garage-info strong {
            color: #1e293b;
        }
        .footer {
            background-color: #0f172a;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <span class="badge-ready">INTERVENTION TERMINÉE</span>
            <h1>Garage Mécano D-CLIC</h1>
            <p>Votre véhicule est inspecté, nettoyé et prêt pour la restitution</p>
        </div>

        <!-- Body -->
        <div class="content">
            <p class="greeting">
                Bonjour <strong>{{ $client ? $client->Prenom . ' ' . $client->Nom : 'Cher Client' }}</strong>,
            </p>
            <p style="font-size: 14px; line-height: 1.6; color: #475569;">
                Nous avons le plaisir de vous informer que l'intervention sur votre véhicule est <strong>entièrement terminée</strong>. Les tests de sécurité et contrôles qualité effectués par notre équipe technique sont conformes.
            </p>

            <!-- Vehicle Summary with Real Photo -->
            <div class="vehicle-box">
                @if($vehicule && $vehicule->photo_url)
                <div style="margin-right: 18px; flex-shrink: 0;">
                    <img src="{{ $vehicule->photo_url }}" alt="{{ $vehicule->brand }}" style="width: 140px; height: 95px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1; display: block;">
                </div>
                @endif
                <div>
                    <h2 class="vehicle-title">
                        {{ $vehicule ? $vehicule->brand . ' ' . $vehicule->model : 'Véhicule' }}
                    </h2>
                    <p class="vehicle-meta">
                        <strong>Immatriculation :</strong> <span style="background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-family: monospace; font-weight: bold; color: #0f172a;">{{ $vehicule ? $vehicule->registration_number : 'N/A' }}</span>
                    </p>
                    <p class="vehicle-meta">
                        <strong>Motorisation :</strong> {{ $vehicule ? $vehicule->fuel_type : 'Essence' }} &bull; 
                        <strong>Kilométrage :</strong> {{ $vehicule ? number_format($vehicule->kilometrage, 0, ',', ' ') : 'N/A' }} km
                    </p>
                    @if($vehicule && $vehicule->boite_vitesse)
                    <p class="vehicle-meta">
                        <strong>Transmission :</strong> {{ $vehicule->boite_vitesse }} &bull; <strong>Année :</strong> {{ $vehicule->annee }}
                    </p>
                    @endif
                </div>
            </div>

            <!-- Work Order Details -->
            <div class="work-box">
                <h3>Travaux réalisés</h3>
                <p><strong>{{ $reparation->titre ?? 'Entretien & Réparation complète' }}</strong></p>
                <p style="margin-top: 6px; font-size: 13px; color: #475569;">
                    {{ $reparation->diagnostic ?? $reparation->description ?? 'Révision générale, remplacement des pièces d\'usure et contrôles des points de sécurité.' }}
                </p>
            </div>

            <!-- Details Table -->
            <table class="info-grid">
                <tr>
                    <td class="info-label">Technicien en charge</td>
                    <td class="info-val">{{ $technicien ? $technicien->Prenom . ' ' . $technicien->Nom : 'Jean Lucha' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Date de mise à disposition</td>
                    <td class="info-val">{{ now()->format('d/m/Y à H:i') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Statut du véhicule</td>
                    <td class="info-val" style="color: #10b981;">Prêt à être récupéré</td>
                </tr>
                @if(isset($reparation->cout_total) && $reparation->cout_total > 0)
                <tr>
                    <td class="info-label">Montant total TTC</td>
                    <td class="info-val" style="font-size: 17px; color: #2563eb;">{{ number_format($reparation->cout_total, 2, ',', ' ') }} €</td>
                </tr>
                @endif
            </table>

            <div class="cta-box">
                <a href="{{ url('/client/dashboard') }}" class="btn-pickup">Accéder à mon Espace Client</a>
            </div>
        </div>

        <!-- Practical Info -->
        <div class="garage-info">
            <strong>Adresse de l'atelier :</strong> {{ config('app.garage_address') }}<br>
            <strong>Contact du garage :</strong> {{ config('mail.from.address') }}<br>
            <strong>Documents requis pour le retrait :</strong> Votre pièce d'identité et un moyen de règlement.
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} Garage Mécano D-CLIC, {{ config('app.garage_address') }}. Tous droits réservés.
        </div>
    </div>
</body>
</html>
