@extends('layouts.app')

@section('title', 'Espace Technicien &bull; Mécano D-CLIC')
@section('page_title', 'Espace Atelier Technicien')
@section('page_subtitle', 'Gestion de vos interventions, diagnostics et mise à disposition des véhicules')

@section('content')
    <!-- Tech Profile Bar -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 20px 24px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; box-shadow: var(--shadow-card);">
        <div style="display: flex; align-items: center; gap: 16px;">
            <x-profile-initials :user="$user" size="58" />
            <div>
                <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 2px 0;">
                    Technicien : {{ $user->Prenom }} {{ $user->Nom }}
                </h2>
                <div style="font-size: 13px; color: var(--color-primary); font-weight: 600; margin-bottom: 2px;">
                    <i class="fa-solid fa-wrench"></i> Spécialité : {{ $user->specialite ?? 'Mécanique générale & Diagnostic' }}
                </div>
                <div style="font-size: 12px; color: var(--text-muted);">
                    Statut : <span style="color: var(--text-primary); font-weight: 600;">{{ ucfirst($user->statut) }}</span> &bull; Atelier à {{ config('app.garage_address') }}
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 12px;">
            <div style="background: #f8fafc; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 8px; text-align: center;">
                <div style="font-size: 20px; font-weight: 700; color: #d97706;">{{ $reparationsEnCours->count() }}</div>
                <div style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">En cours</div>
            </div>
            <div style="background: #f8fafc; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 8px; text-align: center;">
                <div style="font-size: 20px; font-weight: 700; color: #059669;">{{ $reparationsPretes->count() }}</div>
                <div style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Terminées</div>
            </div>
        </div>
    </div>

    <!-- Active Repair Orders Assigned to Technician -->
    <div style="margin-bottom: 32px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-screwdriver-wrench" style="color: #d97706;"></i> Véhicules Actuellement sur Mon Pont / En Atelier
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 20px;">
            @forelse($reparationsEnCours as $r)
            @php 
                $veh = $r->vehicule; 
                $clt = $veh?->client?->user;
            @endphp
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; overflow: hidden; display: flex; flex-direction: column; box-shadow: var(--shadow-card);">
                <!-- Car Header with Image -->
                <div style="height: 160px; background: #0f172a; position: relative; border-bottom: 1px solid var(--border-color);">
                    <img src="{{ $veh?->photo_url }}" alt="Car" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="vehicle-plate-badge" style="top: 12px; left: 12px;">{{ $veh?->registration_number }}</div>
                    
                    @if($r->status === 'diagnostic')
                        <div class="vehicle-status-badge status-badge-diagnostic" style="top: 12px; right: 12px;">Diagnostic</div>
                    @else
                        <div class="vehicle-status-badge status-badge-reparation" style="top: 12px; right: 12px;">En cours</div>
                    @endif
                </div>

                <div style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                        <div>
                            <h4 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px 0;">
                                {{ $veh?->brand }} {{ $veh?->model }}
                            </h4>
                            <div style="font-size: 12px; color: var(--text-muted);">
                                Client : <strong style="color: var(--text-secondary);">{{ $clt?->full_name }}</strong> ({{ $clt?->NumeroTelephone }})
                            </div>
                        </div>
                    </div>

                    <!-- Work Order Title -->
                    <div style="background: #eff6ff; border-left: 3px solid #1d4ed8; padding: 10px 12px; border-radius: 0 6px 6px 0; margin-bottom: 14px;">
                        <div style="font-size: 13px; font-weight: 600; color: #1e40af;">{{ $r->titre }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">{{ $r->description }}</div>
                    </div>

                    <!-- Technical Specs Pills -->
                    <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px;">
                        <span class="badge-pill fuel-badge-essence">{{ $veh?->fuel_type }}</span>
                        <span class="badge-pill" style="background: #f8fafc; color: var(--text-secondary); border: 1px solid var(--border-color);">{{ number_format($veh?->kilometrage, 0, ',', ' ') }} km</span>
                        <span class="badge-pill" style="background: #f8fafc; color: var(--text-secondary); border: 1px solid var(--border-color);">{{ $veh?->boite_vitesse }}</span>
                        <span class="badge-pill" style="background: #f8fafc; color: var(--text-secondary); border: 1px solid var(--border-color);">{{ $veh?->annee }}</span>
                    </div>

                    <!-- Update Intervention Form -->
                    <form action="{{ route('technicien.reparations.updateStatus', $r->id) }}" method="POST" style="margin-top: auto; background: #f8fafc; border: 1px solid var(--border-color); padding: 14px; border-radius: 8px;">
                        @csrf
                        @method('PUT')

                        <div class="form-group" style="margin-bottom: 10px;">
                            <label class="form-label">Rapport / Diagnostic du Technicien</label>
                            <textarea name="diagnostic" rows="2" class="form-control-dark" style="font-size: 12px;" placeholder="Détaillez les réparations effectuées, pièces changées et contrôles...">{{ $r->diagnostic }}</textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                            <div>
                                <label class="form-label">Coût Pièces (€)</label>
                                <input type="number" step="0.01" name="cout_pieces" value="{{ $r->cout_pieces }}" class="form-control-dark" style="padding: 7px 10px; font-size: 12px;">
                            </div>
                            <div>
                                <label class="form-label">Main d'œuvre (€)</label>
                                <input type="number" step="0.01" name="cout_main_oeuvre" value="{{ $r->cout_main_oeuvre }}" class="form-control-dark" style="padding: 7px 10px; font-size: 12px;">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 10px;">
                            <label class="form-label">Passer au Statut Suivant</label>
                            <select name="status" class="form-control-dark" style="padding: 7px 10px; font-size: 12px;" id="techStatusSelect_{{ $r->id }}" onchange="toggleTechEmailOption({{ $r->id }})">
                                <option value="diagnostic" {{ $r->status == 'diagnostic' ? 'selected' : '' }}>Diagnostic en cours</option>
                                <option value="en_cours" {{ $r->status == 'en_cours' ? 'selected' : '' }}>En cours de réparation</option>
                                <option value="prete" {{ $r->status == 'prete' ? 'selected' : '' }}>Terminée / Prête pour le client</option>
                            </select>
                        </div>

                        <!-- Checkbox notification email -->
                        <div id="techEmailBox_{{ $r->id }}" style="display: none; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 8px 12px; border-radius: 6px; margin-bottom: 12px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #065f46; font-weight: 600; font-size: 12px;">
                                <input type="checkbox" name="notifier_client" value="1" checked style="accent-color: #059669;">
                                <span>Notifier automatiquement {{ $clt?->email }} par email</span>
                            </label>
                        </div>

                        <button type="submit" class="btn-primary-glow" style="width: 100%; padding: 8px; font-size: 13px;">
                            <i class="fa-solid fa-floppy-disk"></i> Valider l'Avancement
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: #ffffff; border-radius: 10px; border: 1px solid var(--border-color);">
                <i class="fa-solid fa-mug-hot" style="font-size: 32px; color: #059669; margin-bottom: 10px;"></i>
                <h4 style="font-size: 15px; color: var(--text-primary); font-weight: 600;">Toutes vos interventions sont à jour !</h4>
                <p style="font-size: 13px; color: var(--text-muted);">Aucun véhicule en attente de réparation sur votre pont.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Completed Repairs History by this Tech -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 22px; box-shadow: var(--shadow-card);">
        <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-check" style="color: #059669;"></i> Mes Réparations Terminées & Prêtes Récemment
        </h3>

        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Véhicule</th>
                        <th>Intervention</th>
                        <th>Client</th>
                        <th>Date de Clôture</th>
                        <th>Notification Email</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reparationsPretes as $rp)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-primary);">{{ $rp->vehicule?->brand }} {{ $rp->vehicule?->model }}</div>
                            <div style="font-family: monospace; font-size: 11px; font-weight: 600; color: var(--color-primary);">{{ $rp->vehicule?->registration_number }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-primary);">{{ $rp->titre }}</div>
                        </td>
                        <td>
                            <div style="font-size: 13px; color: var(--text-secondary);">{{ $rp->vehicule?->client?->user?->full_name }}</div>
                        </td>
                        <td style="font-size: 12px; color: var(--text-muted); font-family: monospace;">
                            {{ $rp->date_fin ? $rp->date_fin->format('d/m/Y à H:i') : $rp->updated_at->format('d/m/Y') }}
                        </td>
                        <td>
                            @if($rp->notification_envoyee)
                                <span class="badge-pill" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                                    <i class="fa-solid fa-check-double"></i> Email Prêt Envoyé
                                </span>
                            @else
                                <span class="badge-pill" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a;">
                                    En attente d'envoi
                                </span>
                                @if($rp->status === 'prete' || $rp->status === 'pret')
                                    <form action="{{ route('technicien.reparations.notifier', $rp->id) }}" method="POST" style="margin-top:8px;">
                                        @csrf
                                        <button type="submit" class="btn-primary-glow" style="padding:6px 10px;font-size:11px;">Envoyer l’email au client</button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">
                            Aucune réparation terminée dans l'historique récent.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function toggleTechEmailOption(id) {
        const sel = document.getElementById('techStatusSelect_' + id);
        const box = document.getElementById('techEmailBox_' + id);
        if (sel.value === 'prete') {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }
</script>
@endsection
