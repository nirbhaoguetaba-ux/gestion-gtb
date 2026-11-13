@extends('layouts.app')

@section('title', 'Mon Espace Client &bull; Mécano D-CLIC')
@section('page_title', 'Espace Client & Suivi Véhicule')
@section('page_subtitle', 'Suivez l\'avancement en temps réel des réparations de vos véhicules')

@section('content')
    <!-- Welcome Client Banner -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 20px 24px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; box-shadow: var(--shadow-card);">
        <div style="display: flex; align-items: center; gap: 16px;">
            <x-profile-initials :user="$user" size="58" />
            <div>
                <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 2px 0;">
                    Bonjour, {{ $user->Prenom }} {{ $user->Nom }}
                </h2>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">
                    Bienvenue sur votre portail client dédié. Vous avez <strong style="color: var(--text-primary);">{{ $vehicules->count() }} véhicule(s)</strong> enregistré(s).
                </p>
            </div>
        </div>

        <div style="display: flex; gap: 12px;">
            <div style="background: #f8fafc; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; text-align: center;">
                <div style="font-size: 10px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Service Client Garage</div>
                <div style="font-size: 14px; font-weight: 700; color: var(--color-primary);">+228 90 00 00 01</div>
            </div>
        </div>
    </div>

    <!-- Active Repairs with LIVE STEPPER for each vehicle -->
    @if($reparationsEnCours->count() > 0)
    <div style="margin-bottom: 32px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-satellite-dish" style="color: #059669;"></i> Suivi en Temps Réel de Vos Interventions en Cours
        </h3>

        @foreach($reparationsEnCours as $r)
        @php
            $v = $r->vehicule;
            $tech = $r->mecanique?->user;
            
            // Calculate stepper progression
            $stepIndex = 1;
            if ($r->status === 'diagnostic') $stepIndex = 2;
            elseif ($r->status === 'en_cours') $stepIndex = 3;
            elseif ($r->status === 'prete' || $r->status === 'pret') $stepIndex = 4;
            $progressPercent = ($stepIndex - 1) * 33.33;
        @endphp
        <div class="stepper-container" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 24px; margin-bottom: 20px; box-shadow: var(--shadow-card);">
            <!-- Vehicle Info & Ready Alert -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 60px; height: 44px; border-radius: 6px; overflow: hidden; background: #e2e8f0; border: 1px solid var(--border-color);">
                        <img src="{{ $v?->photo_url }}" alt="Car" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <h4 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 2px 0;">
                            {{ $v?->brand }} {{ $v?->model }}
                        </h4>
                        <div style="font-family: monospace; font-size: 12px; font-weight: 600; color: var(--color-primary);">
                            Immatriculation : <strong>{{ $v?->registration_number }}</strong>
                        </div>
                    </div>
                </div>

                @if($stepIndex === 4)
                    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; padding: 10px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-circle-check" style="color: #059669; font-size: 18px;"></i>
                        <div>
                            <div style="font-size: 13px; font-weight: 700; color: #065f46;">VOTRE VÉHICULE EST PRÊT</div>
                            <div style="font-size: 12px; color: #047857;">Intervention terminée. Vous pouvez venir le retirer.</div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Interactive Visual Stepper -->
            <div class="stepper-wrapper">
                <div class="stepper-progress-bar" style="width: {{ $progressPercent }}%;"></div>

                <div class="step-item {{ $stepIndex >= 1 ? ($stepIndex > 1 ? 'completed' : 'active') : '' }}">
                    <div class="step-bubble">
                        @if($stepIndex > 1) <i class="fa-solid fa-check"></i> @else <i class="fa-solid fa-receipt"></i> @endif
                    </div>
                    <div class="step-label">1. Réceptionné</div>
                </div>

                <div class="step-item {{ $stepIndex >= 2 ? ($stepIndex > 2 ? 'completed' : 'active') : '' }}">
                    <div class="step-bubble">
                        @if($stepIndex > 2) <i class="fa-solid fa-check"></i> @else <i class="fa-solid fa-magnifying-glass"></i> @endif
                    </div>
                    <div class="step-label">2. Diagnostic</div>
                </div>

                <div class="step-item {{ $stepIndex >= 3 ? ($stepIndex > 3 ? 'completed' : 'active') : '' }}">
                    <div class="step-bubble">
                        @if($stepIndex > 3) <i class="fa-solid fa-check"></i> @else <i class="fa-solid fa-wrench"></i> @endif
                    </div>
                    <div class="step-label">3. En Atelier</div>
                </div>

                <div class="step-item {{ $stepIndex >= 4 ? 'completed active' : '' }}">
                    <div class="step-bubble" style="{{ $stepIndex == 4 ? 'background: #ecfdf5; border-color: #059669; color: #059669;' : '' }}">
                        <i class="fa-solid fa-car-side"></i>
                    </div>
                    <div class="step-label" style="{{ $stepIndex == 4 ? 'color: #065f46; font-weight: 700;' : '' }}">
                        4. Prêt pour Retrait
                    </div>
                </div>
            </div>

            <!-- Work Order Summary Card -->
            <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 18px; display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px;">
                <div>
                    <div style="font-size: 11px; font-weight: 600; color: var(--color-primary); text-transform: uppercase;">Travaux en cours :</div>
                    <div style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 3px 0;">{{ $r->titre }}</div>
                    <p style="font-size: 13px; color: var(--text-secondary); margin: 0 0 10px 0;">{{ $r->description }}</p>
                    
                    @if($r->diagnostic)
                        <div style="background: #eff6ff; border-left: 3px solid #1d4ed8; padding: 8px 12px; border-radius: 0 6px 6px 0; font-size: 12px; color: #1e40af;">
                            <strong>Note du mécanicien :</strong> {{ $r->diagnostic }}
                        </div>
                    @endif
                </div>

                <div style="border-left: 1px solid var(--border-color); padding-left: 20px; display: flex; flex-direction: column; justify-content: center;">
                    <div style="font-size: 11px; color: var(--text-muted);">Technicien référent</div>
                    <div style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 10px;">
                        {{ $tech?->full_name ?? 'Atelier Mécano D-CLIC' }}
                    </div>

                    <div style="font-size: 11px; color: var(--text-muted);">Montant estimé TTC</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--color-primary);">
                        {{ number_format($r->cout_total, 2, ',', ' ') }} FCFA
                    </div>

                    @if($stepIndex === 4)
                        <div style="margin-top: 8px;">
                            <span class="badge-pill" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 11px;">
                                <i class="fa-solid fa-envelope-circle-check"></i> E-mail de notification transmis
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pickup Practical Instructions -->
            @if($stepIndex === 4)
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 14px 18px; margin-top: 14px; font-size: 13px; color: #065f46; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                <div>
                    <strong style="color: #065f46;"><i class="fa-solid fa-location-dot"></i> Atelier de Retrait :</strong> 148 Avenue des Garages, 75011 Paris<br>
                    <span><strong>Horaires :</strong> Du Lundi au Samedi de 08h00 à 19h00 sans interruption.</span>
                </div>
                <a href="tel:0145678910" class="btn-success-glow" style="padding: 7px 14px; font-size: 12px;">
                    <i class="fa-solid fa-phone"></i> Appeler l'Atelier
                </a>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- My Registered Vehicles Showcase with Photos & Specs -->
    <div>
        <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-car" style="color: var(--color-primary);"></i> Mes Véhicules Enregistrés
        </h3>

        <div class="vehicle-grid">
            @forelse($vehicules as $v)
            <div class="vehicle-card">
                <div class="vehicle-card-img-wrap">
                    <img src="{{ $v->photo_url }}" alt="{{ $v->brand }}">
                    <div class="vehicle-plate-badge">{{ $v->registration_number }}</div>

                    @if($v->statut === 'pret' || $v->statut === 'prete')
                        <div class="vehicle-status-badge status-badge-pret"><i class="fa-solid fa-check"></i> Prêt</div>
                    @elseif($v->statut === 'en_reparation' || $v->statut === 'en_cours')
                        <div class="vehicle-status-badge status-badge-reparation"><i class="fa-solid fa-wrench"></i> En Réparation</div>
                    @else
                        <div class="vehicle-status-badge status-badge-attente"><i class="fa-solid fa-clock"></i> En Attente</div>
                    @endif
                </div>

                <div class="vehicle-card-body">
                    <h3 class="vehicle-title">{{ $v->brand }} {{ $v->model }}</h3>
                    <div class="vehicle-client">
                        <i class="fa-solid fa-fingerprint"></i> N° VIN : {{ $v->vin ?? 'VF3CCHNZ6ET102938' }}
                    </div>

                    <!-- Characteristics Specs -->
                    <div class="specs-grid">
                        <div class="spec-item">
                            <span class="spec-label">Carburant</span>
                            <span class="spec-value">{{ $v->fuel_type }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Année</span>
                            <span class="spec-value">{{ $v->annee }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Kilométrage</span>
                            <span class="spec-value">{{ number_format($v->kilometrage, 0, ',', ' ') }} km</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Boîte</span>
                            <span class="spec-value">{{ $v->boite_vitesse }}</span>
                        </div>
                        <div class="spec-item" style="grid-column: span 2;">
                            <span class="spec-label">Couleur</span>
                            <span class="spec-value">{{ $v->couleur }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: #ffffff; border-radius: 10px; border: 1px solid var(--border-color);">
                <i class="fa-solid fa-car" style="font-size: 32px; color: var(--text-muted); margin-bottom: 10px;"></i>
                <h4 style="font-size: 15px; color: var(--text-primary); font-weight: 600;">Aucun véhicule associé à votre compte</h4>
                <p style="font-size: 13px; color: var(--text-muted);">Contactez la réception du garage pour associer votre véhicule.</p>
            </div>
            @endforelse
        </div>
    </div>
@endsection
