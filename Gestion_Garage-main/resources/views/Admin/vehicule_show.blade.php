@extends('layouts.app')

@section('title', $vehicule->brand . ' ' . $vehicule->model . ' &bull; Fiche Technique')
@section('page_title', $vehicule->brand . ' ' . $vehicule->model)
@section('page_subtitle', 'Fiche technique détaillée et carnet d\'interventions')

@section('top_buttons')
    <a href="{{ route('admin.vehicules') }}" class="btn-outline-dark">
        <i class="fa-solid fa-arrow-left"></i> Retour au parc
    </a>
    <button type="button" class="btn-primary-glow" onclick="document.getElementById('newRepModal').style.display = 'flex'">
        <i class="fa-solid fa-plus"></i> Nouvelle intervention
    </button>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; margin-bottom: 30px;">
        <!-- Fiche technique & Photo réelle -->
        <div>
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; margin-bottom: 20px;">
                <div style="height: 320px; background: #0f172a; position: relative; overflow: hidden;">
                    <img src="{{ $vehicule->photo_url }}" alt="{{ $vehicule->brand }}" style="width: 100%; height: 100%; object-fit: cover;">
                    
                    <div class="vehicle-plate-badge" style="top: 14px; left: 14px; font-size: 13px; padding: 5px 10px;">
                        {{ $vehicule->registration_number }}
                    </div>

                    @if($vehicule->statut === 'pret' || $vehicule->statut === 'prete')
                        <div class="vehicle-status-badge status-badge-pret" style="top: 14px; right: 14px;">
                            Prêt pour restitution
                        </div>
                    @elseif($vehicule->statut === 'en_reparation' || $vehicule->statut === 'en_cours')
                        <div class="vehicle-status-badge status-badge-reparation" style="top: 14px; right: 14px;">
                            En cours de réparation
                        </div>
                    @else
                        <div class="vehicle-status-badge status-badge-attente" style="top: 14px; right: 14px;">
                            En attente de prise en charge
                        </div>
                    @endif
                </div>

                <div style="padding: 20px;">
                    <h2 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">
                        {{ $vehicule->brand }} {{ $vehicule->model }}
                    </h2>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">
                        Véhicule enregistré dans le système le {{ $vehicule->created_at->format('d/m/Y') }}.
                    </p>
                </div>
            </div>

            <!-- Tableau des spécifications techniques -->
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 16px;">
                    Caractéristiques d'origine
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="spec-item" style="background: #f8fafc; padding: 10px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <span class="spec-label">Numéro d'immatriculation</span>
                        <span class="spec-value" style="font-family: monospace; font-size: 13px;">{{ $vehicule->registration_number }}</span>
                    </div>

                    <div class="spec-item" style="background: #f8fafc; padding: 10px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <span class="spec-label">Numéro de châssis (VIN)</span>
                        <span class="spec-value" style="font-family: monospace; font-size: 12px;">{{ $vehicule->vin ?? 'Non renseigné' }}</span>
                    </div>

                    <div class="spec-item" style="background: #f8fafc; padding: 10px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <span class="spec-label">Carburant</span>
                        <span class="spec-value">{{ $vehicule->fuel_type }}</span>
                    </div>

                    <div class="spec-item" style="background: #f8fafc; padding: 10px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <span class="spec-label">Année</span>
                        <span class="spec-value">{{ $vehicule->annee }}</span>
                    </div>

                    <div class="spec-item" style="background: #f8fafc; padding: 10px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <span class="spec-label">Kilométrage</span>
                        <span class="spec-value">{{ number_format($vehicule->kilometrage, 0, ',', ' ') }} km</span>
                    </div>

                    <div class="spec-item" style="background: #f8fafc; padding: 10px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <span class="spec-label">Transmission</span>
                        <span class="spec-value">{{ $vehicule->boite_vitesse }}</span>
                    </div>

                    <div class="spec-item" style="background: #f8fafc; padding: 10px 12px; border-radius: 6px; border: 1px solid #e2e8f0; grid-column: span 2;">
                        <span class="spec-label">Couleur extérieure</span>
                        <span class="spec-value">{{ $vehicule->couleur }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations Client & Actions rapides -->
        <div>
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 14px;">
                    Client propriétaire
                </h3>

                @php $clientUser = $vehicule->client?->user; @endphp
                @if($clientUser)
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <x-profile-initials :user="$clientUser" size="44" />
                        <div>
                            <div style="font-size: 15px; font-weight: 600; color: #0f172a;">{{ $clientUser->full_name }}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Compte vérifié</div>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                        <div>
                            <div style="color: var(--text-muted); font-size: 11px;">E-mail</div>
                            <a href="mailto:{{ $clientUser->email }}" style="color: #1d4ed8; text-decoration: none;">{{ $clientUser->email }}</a>
                        </div>
                        <div>
                            <div style="color: var(--text-muted); font-size: 11px;">Téléphone</div>
                            <div style="color: #0f172a; font-weight: 500;">{{ $clientUser->NumeroTelephone }}</div>
                        </div>
                        <div>
                            <div style="color: var(--text-muted); font-size: 11px;">Adresse</div>
                            <div style="color: #334155;">{{ $clientUser->Adresse ?? 'Non renseignée' }}</div>
                        </div>
                    </div>
                @else
                    <p style="color: #64748b; font-size: 13px;">Aucun client rattaché.</p>
                @endif
            </div>

            <!-- Mise à jour rapide du statut -->
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; padding: 20px;">
                <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 14px;">
                    Modifier le statut
                </h3>

                <form action="{{ route('admin.vehicules.update', $vehicule->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="brand" value="{{ $vehicule->brand }}">
                    <input type="hidden" name="model" value="{{ $vehicule->model }}">
                    <input type="hidden" name="annee" value="{{ $vehicule->annee }}">
                    <input type="hidden" name="fuel_type" value="{{ $vehicule->fuel_type }}">
                    <input type="hidden" name="registration_number" value="{{ $vehicule->registration_number }}">
                    <input type="hidden" name="vin" value="{{ $vehicule->vin }}">
                    <input type="hidden" name="boite_vitesse" value="{{ $vehicule->boite_vitesse }}">
                    <input type="hidden" name="couleur" value="{{ $vehicule->couleur }}">

                    <div class="form-group">
                        <label class="form-label">Kilométrage (km)</label>
                        <input type="number" name="kilometrage" class="form-control-dark" value="{{ $vehicule->kilometrage }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Statut en atelier</label>
                        <select name="statut" class="form-control-dark">
                            <option value="en_attente" {{ $vehicule->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="en_reparation" {{ $vehicule->statut == 'en_reparation' || $vehicule->statut == 'en_cours' ? 'selected' : '' }}>En réparation</option>
                            <option value="pret" {{ $vehicule->statut == 'pret' || $vehicule->statut == 'prete' ? 'selected' : '' }}>Prêt pour restitution</option>
                            <option value="livre" {{ $vehicule->statut == 'livre' ? 'selected' : '' }}>Restitué</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary-glow" style="width: 100%;">
                        Mettre à jour
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Historique des réparations sur ce véhicule -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; padding: 20px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 16px; display: flex; justify-content: space-between;">
            <span>Historique des interventions sur ce véhicule</span>
            <span style="font-size: 13px; color: #64748b; font-weight: 400;">{{ $vehicule->reparations->count() }} intervention(s)</span>
        </h3>

        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Intitulé des travaux</th>
                        <th>Technicien</th>
                        <th>Statut</th>
                        <th>Montant TTC</th>
                        <th>Notification e-mail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicule->reparations as $rep)
                    <tr>
                        <td style="font-size: 12px; color: #64748b;">
                            {{ $rep->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #0f172a;">{{ $rep->titre }}</div>
                            <div style="font-size: 12px; color: #64748b;">{{ $rep->description }}</div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if($rep->mecanique?->user)
                                    <x-profile-initials :user="$rep->mecanique->user" size="24" />
                                @endif
                                <span style="font-size: 12px;">{{ $rep->mecanique?->user?->Prenom ?? 'Non assigné' }}</span>
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
                            @else
                                <span class="badge-pill" style="background: #f1f5f9; color: #475569;">
                                    {{ $rep->status }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #0f172a;">{{ number_format($rep->cout_total, 2, ',', ' ') }} €</span>
                        </td>
                        <td>
                            @if($rep->notification_envoyee)
                                <span class="badge-pill" style="background: #ecfdf5; color: #065f46;">
                                    Envoyée le {{ $rep->notification_envoyee_at?->format('d/m à H:i') }}
                                </span>
                            @elseif($rep->status === 'prete' || $rep->status === 'pret')
                                <form action="{{ route('admin.reparations.notifier', $rep->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-success-glow" style="padding: 5px 10px; font-size: 11px;">
                                        <i class="fa-regular fa-envelope"></i> Notifier
                                    </button>
                                </form>
                            @else
                                <span style="font-size: 11px; color: #94a3b8;">En intervention</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">
                            Aucune réparation sur ce véhicule.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modale Nouvelle Réparation -->
    <div id="newRepModal" class="modal-overlay">
        <div class="modal-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <h2 style="font-size: 17px; font-weight: 700; color: #0f172a;">
                    Ouvrir un ordre de réparation
                </h2>
                <button type="button" onclick="document.getElementById('newRepModal').style.display='none'" style="background: none; border: none; color: #64748b; font-size: 18px; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.reparations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="vehicule_id" value="{{ $vehicule->id }}">

                <div class="form-group">
                    <label class="form-label">Intitulé de l'intervention</label>
                    <input type="text" name="titre" class="form-control-dark" placeholder="ex: Révision complète, Remplacement plaquettes..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Technicien en charge</label>
                    <select name="mecanique_id" class="form-control-dark">
                        <option value="">Attribuer plus tard...</option>
                        @foreach($techniciens as $t)
                            <option value="{{ $t->id }}">{{ $t->user?->full_name }} ({{ $t->specialisation }})</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label">Priorité</label>
                        <select name="priorite" class="form-control-dark">
                            <option value="normale">Normale</option>
                            <option value="urgente">Urgente</option>
                            <option value="basse">Basse</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Statut initial</label>
                        <select name="status" class="form-control-dark">
                            <option value="en_attente">En attente</option>
                            <option value="diagnostic">Diagnostic</option>
                            <option value="en_cours">En cours</option>
                            <option value="prete">Prête</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estimation pièces (€)</label>
                        <input type="number" step="0.01" name="cout_pieces" class="form-control-dark" value="0.00">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estimation main d'œuvre (€)</label>
                        <input type="number" step="0.01" name="cout_main_oeuvre" class="form-control-dark" value="0.00">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description des symptômes et travaux</label>
                    <textarea name="description" rows="3" class="form-control-dark" placeholder="Remarques signalées par le client..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; border-top: 1px solid var(--border-color); padding-top: 14px;">
                    <button type="button" onclick="document.getElementById('newRepModal').style.display='none'" class="btn-outline-dark">Annuler</button>
                    <button type="submit" class="btn-primary-glow">Créer l'ordre</button>
                </div>
            </form>
        </div>
    </div>
@endsection
