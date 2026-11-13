@extends('layouts.app')

@section('title', 'Équipe des Techniciens &bull; Mécano D-CLIC')
@section('page_title', 'Équipe Technique & Mécaniciens')
@section('page_subtitle', 'Spécialisations, disponibilités et réparations en cours par technicien')

@section('top_buttons')
    <button type="button" class="btn-primary-glow" onclick="document.getElementById('addTechModal').style.display = 'flex'">
        <i class="fa-solid fa-user-plus"></i> Ajouter un Technicien
    </button>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px;">
        @foreach($techniciens as $t)
        @php
            $activeReps = $t->mecanique?->reparations->whereIn('status', ['en_cours', 'diagnostic', 'en_attente']) ?? collect();
            $completedReps = $t->mecanique?->reparations->whereIn('status', ['prete', 'terminee']) ?? collect();
        @endphp
        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 22px; box-shadow: var(--shadow-card); display: flex; flex-direction: column;">
            <!-- Tech Header -->
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 18px;">
                <x-profile-initials :user="$t" size="58" />
                <div style="flex: 1;">
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 3px 0;">
                        {{ $t->Prenom }} {{ $t->Nom }}
                    </h3>
                    <div style="font-size: 12px; font-weight: 600; color: var(--color-primary); margin-bottom: 6px;">
                        <i class="fa-solid fa-wrench"></i> {{ $t->specialite ?? $t->mecanique?->specialisation ?? 'Mécanique générale' }}
                    </div>
                    @if($t->statut == 'disponible')
                        <span class="badge-pill" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 11px;">
                            <i class="fa-solid fa-circle" style="font-size: 7px;"></i> Disponible
                        </span>
                    @else
                        <span class="badge-pill" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 11px;">
                            <i class="fa-solid fa-screwdriver-wrench"></i> En intervention
                        </span>
                    @endif
                </div>
            </div>

            <!-- Tech Contact Info -->
            <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px 14px; margin-bottom: 18px; font-size: 13px; display: flex; flex-direction: column; gap: 6px;">
                <div style="display: flex; align-items: center; gap: 10px; color: var(--text-secondary);">
                    <i class="fa-regular fa-envelope" style="color: var(--text-muted); width: 16px;"></i>
                    <span>{{ $t->email }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; color: var(--text-secondary);">
                    <i class="fa-solid fa-phone" style="color: var(--text-muted); width: 16px;"></i>
                    <span>{{ $t->NumeroTelephone ?? '+228 ' }}</span>
                </div>
            </div>

            <!-- Workload Stats -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px;">
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px; text-align: center;">
                    <div style="font-size: 20px; font-weight: 700; color: #1d4ed8;">{{ $activeReps->count() }}</div>
                    <div style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">En cours</div>
                </div>
                <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 10px; text-align: center;">
                    <div style="font-size: 20px; font-weight: 700; color: #059669;">{{ $completedReps->count() }}</div>
                    <div style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Terminées</div>
                </div>
            </div>

            <!-- Active Jobs on this tech -->
            <div style="margin-top: auto;">
                <div style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">
                    Interventions en cours :
                </div>
                @if($activeReps->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        @foreach($activeReps->take(2) as $r)
                        <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid var(--border-color); padding: 8px 10px; border-radius: 6px; font-size: 12px;">
                            <span style="color: var(--text-primary); font-weight: 600;">{{ $r->vehicule?->brand }} {{ $r->vehicule?->model }}</span>
                            <span style="color: var(--color-primary); font-family: monospace; font-size: 11px; font-weight: 600;">{{ $r->vehicule?->registration_number }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="font-size: 12px; color: var(--text-muted); font-style: italic;">
                        Aucun véhicule sur pont actuellement. Disponible.
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Ajouter un Technicien -->
    <div id="addTechModal" class="modal-overlay">
        <div class="modal-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 14px;">
                <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">
                    <i class="fa-solid fa-user-gear" style="color: var(--color-primary);"></i> Ajouter un Nouveau Technicien
                </h2>
                <button type="button" onclick="document.getElementById('addTechModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 20px; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.techniciens.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Nom de famille</label>
                        <input type="text" name="Nom" class="form-control-dark" placeholder="ex: Laurent" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="Prenom" class="form-control-dark" placeholder="ex: Julien" required>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Adresse E-mail professionnelle</label>
                        <input type="email" name="email" class="form-control-dark" placeholder="ex: julien.laurent@garage.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Numéro de Téléphone</label>
                        <input type="text" name="NumeroTelephone" class="form-control-dark" placeholder="ex: +228 90 00 00 00" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mot de Passe initial</label>
                        <input type="password" name="password" class="form-control-dark" minlength="10" autocomplete="new-password" required>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Spécialité Technique</label>
                        <select name="specialite" class="form-control-dark" required>
                            <option value="Moteur, Transmission & Freinage">Moteur, Transmission & Freinage</option>
                            <option value="Diagnostic Électronique & Systèmes Hybrides">Diagnostic Électronique & Systèmes Hybrides</option>
                            <option value="Climatisation, Injection & Échappement">Climatisation, Injection & Échappement</option>
                            <option value="Carrosserie, Géométrie & Peinture">Carrosserie, Géométrie & Peinture</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Disponibilité</label>
                        <select name="statut" class="form-control-dark" required>
                            <option value="disponible">Disponible en atelier</option>
                            <option value="en_intervention">En intervention</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border); padding-top: 18px;">
                    <button type="button" onclick="document.getElementById('addTechModal').style.display='none'" class="btn-outline-dark">Annuler</button>
                    <button type="submit" class="btn-success-glow">Créer le Profil Technicien</button>
                </div>
            </form>
        </div>
    </div>
@endsection
