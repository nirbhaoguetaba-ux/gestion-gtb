@extends('layouts.app')

@section('title', 'Tableau de bord &bull; Administrateur')
@section('page_title', 'Vue d\'ensemble de l\'atelier')
@section('page_subtitle', 'Supervision des réparations, véhicules et charge des techniciens')

@section('top_buttons')
    <a href="{{ route('admin.vehicules') }}#addVehicleModal" class="btn-primary-glow">
        <i class="fa-solid fa-plus"></i> Nouveau véhicule
    </a>
    <a href="{{ route('admin.reparations') }}" class="btn-outline-dark">
        <i class="fa-solid fa-wrench"></i> Gérer les réparations
    </a>
@endsection

@section('content')
    <!-- Indicateurs clés -->
    <div class="kpi-grid">
        <div class="kpi-card" style="--card-accent: #a95432;">
            <div class="kpi-header">
                <span class="kpi-title">Véhicules enregistrés</span>
                <div class="kpi-icon"><i class="fa-solid fa-car"></i></div>
            </div>
            <div class="kpi-value">{{ $totalVehicules }}</div>
            <div class="kpi-subtext">Total du parc géré</div>
        </div>

        <div class="kpi-card" style="--card-accent: #b77935;">
            <div class="kpi-header">
                <span class="kpi-title">En réparation</span>
                <div class="kpi-icon"><i class="fa-solid fa-wrench"></i></div>
            </div>
            <div class="kpi-value">{{ $vehiculesEnReparation }}</div>
            <div class="kpi-subtext">Interventions sur pont</div>
        </div>

        <div class="kpi-card" style="--card-accent: #059669;">
            <div class="kpi-header">
                <span class="kpi-title">Véhicules prêts</span>
                <div class="kpi-icon"><i class="fa-solid fa-check"></i></div>
            </div>
            <div class="kpi-value" style="color: #059669;">{{ $vehiculesPrets }}</div>
            <div class="kpi-subtext">Prêts pour restitution</div>
        </div>

        <div class="kpi-card" style="--card-accent: #475569;">
            <div class="kpi-header">
                <span class="kpi-title">Techniciens actifs</span>
                <div class="kpi-icon"><i class="fa-solid fa-user-gear"></i></div>
            </div>
            <div class="kpi-value">{{ $totalTechniciens }}</div>
            <div class="kpi-subtext">Équipe technique atelier</div>
        </div>
    </div>

    <!-- Notification d'actions si véhicules prêts -->
    @if($vehiculesPrets > 0)
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 36px; height: 36px; border-radius: 6px; background: #059669; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div>
                <div style="font-weight: 600; color: #065f46; font-size: 14px;">
                    {{ $vehiculesPrets }} véhicule(s) ont terminé leur intervention et sont prêts.
                </div>
                <div style="font-size: 13px; color: #047857;">
                    Vous pouvez transmettre la notification par e-mail au client pour l'informer de la mise à disposition.
                </div>
            </div>
        </div>
        <a href="{{ route('admin.reparations') }}?status=prete" class="btn-success-glow" style="white-space: nowrap; font-size: 12px; padding: 7px 14px;">
            <i class="fa-regular fa-envelope"></i> Notifier les clients
        </a>
    </div>
    @endif

    <!-- Ordres de réparation récents -->
    <div style="margin-bottom: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div>
                <h2 style="font-size: 16px; font-weight: 700; color: #0f172a;">Ordres de réparation récents</h2>
                <p style="font-size: 12px; color: #64748b;">Suivi des travaux mécaniques et état des notifications</p>
            </div>
            <a href="{{ route('admin.reparations') }}" class="btn-outline-dark" style="font-size: 12px; padding: 6px 12px;">
                Voir tout le registre <i class="fa-solid fa-arrow-right" style="font-size: 10px;"></i>
            </a>
        </div>

        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Véhicule</th>
                        <th>Travaux planifiés</th>
                        <th>Client</th>
                        <th>Technicien</th>
                        <th>Statut</th>
                        <th>Total TTC</th>
                        <th>Notification e-mail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reparationsRecentes as $rep)
                    @php
                        $veh = $rep->vehicule;
                        $clt = $veh?->client?->user;
                        $tech = $rep->mecanique?->user;
                    @endphp
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 52px; height: 38px; border-radius: 4px; overflow: hidden; background: #e2e8f0; flex-shrink: 0; border: 1px solid #cbd5e1;">
                                    <img src="{{ $veh?->photo_url }}" alt="Photo véhicule" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #0f172a;">{{ $veh?->brand }} {{ $veh?->model }}</div>
                                    <div style="font-family: monospace; font-size: 11px; color: #64748b;">{{ $veh?->registration_number }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 500; color: #1e293b;">{{ $rep->titre }}</div>
                            <div style="font-size: 12px; color: #64748b; max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $rep->description }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $clt?->full_name }}</div>
                            <div style="font-size: 11px; color: #64748b;">{{ $clt?->email }}</div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if($tech)
                                    <x-profile-initials :user="$tech" size="26" />
                                @endif
                                <span style="font-size: 12px; color: #334155;">{{ $tech?->Prenom ?? 'Non assigné' }}</span>
                            </div>
                        </td>
                        <td>
                            @if($rep->status === 'prete' || $rep->status === 'pret')
                                <span class="badge-pill" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                                    Prête
                                </span>
                            @elseif($rep->status === 'en_cours')
                                <span class="badge-pill" style="background: #fefce8; color: #854d0e; border: 1px solid #fef08a;">
                                    En atelier
                                </span>
                            @elseif($rep->status === 'diagnostic')
                                <span class="badge-pill" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;">
                                    Diagnostic
                                </span>
                            @else
                                <span class="badge-pill" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                                    En attente
                                </span>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #0f172a;">
                                {{ number_format($rep->cout_total, 2, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                @if($rep->status === 'prete' || $rep->status === 'pret')
                                    <form action="{{ route('admin.reparations.notifier', $rep->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-success-glow" style="padding: 5px 10px; font-size: 11px;" title="Envoyer notification par email">
                                            <i class="fa-regular fa-envelope"></i> Notifier
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.previewMail', $rep->id) }}" target="_blank" class="btn-outline-dark" style="padding: 5px 8px; font-size: 11px;" title="Aperçu de l'email">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @else
                                    <span style="font-size: 12px; color: #94a3b8;">En cours</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748b; padding: 24px;">
                            Aucune intervention récente.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Galerie des véhicules réels au garage -->
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div>
                <h2 style="font-size: 16px; font-weight: 700; color: #0f172a;">Véhicules en atelier</h2>
                <p style="font-size: 12px; color: #64748b;">Fiches techniques, caractéristiques détaillées et état d'avancement</p>
            </div>
            <a href="{{ route('admin.vehicules') }}" class="btn-outline-dark" style="font-size: 12px; padding: 6px 12px;">
                Consulter le parc <i class="fa-solid fa-arrow-right" style="font-size: 10px;"></i>
            </a>
        </div>

        <div class="vehicle-grid">
            @foreach($vehicules->take(6) as $v)
            <div class="vehicle-card">
                <div class="vehicle-card-img-wrap">
                    <img src="{{ $v->photo_url }}" alt="{{ $v->brand }} {{ $v->model }}">
                    <div class="vehicle-plate-badge">{{ $v->registration_number }}</div>
                    
                    @if($v->statut === 'pret' || $v->statut === 'prete')
                        <div class="vehicle-status-badge status-badge-pret">Prêt</div>
                    @elseif($v->statut === 'en_reparation' || $v->statut === 'en_cours')
                        <div class="vehicle-status-badge status-badge-reparation">En atelier</div>
                    @else
                        <div class="vehicle-status-badge status-badge-attente">En attente</div>
                    @endif
                </div>

                <div class="vehicle-card-body">
                    <h3 class="vehicle-title">{{ $v->brand }} {{ $v->model }}</h3>
                    <div class="vehicle-client">
                        Client : <strong>{{ $v->client?->user?->full_name }}</strong>
                    </div>

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
                            <span class="spec-label">Transmission</span>
                            <span class="spec-value">{{ $v->boite_vitesse }}</span>
                        </div>
                    </div>

                    <div class="vehicle-card-footer">
                        <a href="{{ route('admin.vehicules.show', $v->id) }}" class="btn-outline-dark" style="flex: 1; padding: 7px 12px; font-size: 12px;">
                            Fiche détaillée
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection
