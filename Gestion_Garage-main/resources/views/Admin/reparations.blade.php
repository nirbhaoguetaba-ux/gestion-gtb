@extends('layouts.app')

@section('title', 'Gestion des Réparations &bull; Mécano D-CLIC')
@section('page_title', 'Ordres de Réparation & Interventions')
@section('page_subtitle', 'Suivi de la chaîne de travail, assignation des techniciens et notifications clients')

@section('content')
    <!-- Status Filter Bar -->
    <div style="display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap;">
        <a href="{{ route('admin.reparations') }}" class="btn-outline-dark {{ !request('status') ? 'btn-primary-glow' : '' }}" style="font-size: 13px;">
            Toutes les interventions
        </a>
        <a href="{{ route('admin.reparations') }}?status=en_attente" class="btn-outline-dark {{ request('status') == 'en_attente' ? 'btn-primary-glow' : '' }}" style="font-size: 13px;">
            <i class="fa-solid fa-clock"></i> En Attente
        </a>
        <a href="{{ route('admin.reparations') }}?status=diagnostic" class="btn-outline-dark {{ request('status') == 'diagnostic' ? 'btn-primary-glow' : '' }}" style="font-size: 13px;">
            <i class="fa-solid fa-magnifying-glass"></i> Diagnostic
        </a>
        <a href="{{ route('admin.reparations') }}?status=en_cours" class="btn-outline-dark {{ request('status') == 'en_cours' ? 'btn-primary-glow' : '' }}" style="font-size: 13px;">
            <i class="fa-solid fa-wrench"></i> En Atelier
        </a>
        <a href="{{ route('admin.reparations') }}?status=prete" class="btn-outline-dark {{ request('status') == 'prete' ? 'btn-success-glow' : '' }}" style="font-size: 13px;">
            <i class="fa-solid fa-circle-check"></i> Prêtes (À Notifier)
        </a>
    </div>

    <!-- Repairs Table -->
    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Véhicule</th>
                    <th>Intervention & Diagnostic</th>
                    <th>Client</th>
                    <th>Technicien</th>
                    <th>Statut</th>
                    <th>Total TTC</th>
                    <th>Notification Client par E-mail</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reparations as $rep)
                @php
                    $veh = $rep->vehicule;
                    $clt = $veh?->client?->user;
                    $tech = $rep->mecanique?->user;
                @endphp
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 52px; height: 38px; border-radius: 6px; overflow: hidden; background: #e2e8f0; flex-shrink: 0; border: 1px solid var(--border-color);">
                                <img src="{{ $veh?->photo_url }}" alt="Car" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <div style="font-weight: 700; color: var(--text-primary);">{{ $veh?->brand }} {{ $veh?->model }}</div>
                                <div style="font-family: monospace; font-size: 11px; font-weight: 600; color: var(--color-primary);">{{ $veh?->registration_number }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $rep->titre }}</div>
                        @if($rep->diagnostic)
                            <div style="font-size: 12px; color: var(--color-primary); margin-top: 3px;">
                                <i class="fa-solid fa-notes-medical"></i> {{ Str::limit($rep->diagnostic, 65) }}
                            </div>
                        @else
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 3px;">
                                {{ Str::limit($rep->description, 65) }}
                            </div>
                        @endif
                    </td>

                    <td>
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $clt?->full_name }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">{{ $clt?->email }}</div>
                    </td>

                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if($tech)
                                <x-profile-initials :user="$tech" size="28" />
                            @endif
                            <div>
                                <div style="font-weight: 600; color: var(--text-primary); font-size: 13px;">{{ $tech?->Prenom ?? 'Non assigné' }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $tech?->specialite ? Str::limit($tech->specialite, 20) : 'Atelier' }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        @if($rep->status === 'prete' || $rep->status === 'pret')
                            <span class="badge-pill" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                                <i class="fa-solid fa-circle-check"></i> Prête
                            </span>
                        @elseif($rep->status === 'en_cours')
                            <span class="badge-pill" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a;">
                                <i class="fa-solid fa-wrench"></i> En cours
                            </span>
                        @elseif($rep->status === 'diagnostic')
                            <span class="badge-pill" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                <i class="fa-solid fa-magnifying-glass"></i> Diagnostic
                            </span>
                        @else
                            <span class="badge-pill" style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;">
                                <i class="fa-solid fa-clock"></i> En attente
                            </span>
                        @endif
                    </td>

                    <td>
                        <div style="font-weight: 700; color: var(--color-primary); font-size: 14px;">
                            {{ number_format($rep->cout_total, 2, ',', ' ') }} €
                        </div>
                        <div style="font-size: 11px; color: var(--text-muted);">
                            P: {{ number_format($rep->cout_pieces, 0) }}€ | MO: {{ number_format($rep->cout_main_oeuvre, 0) }}€
                        </div>
                    </td>

                    <td>
                        <!-- Email Notification Action -->
                        @if($rep->notification_envoyee)
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span class="badge-pill" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                                    <i class="fa-solid fa-check-double"></i> Email envoyé
                                </span>
                                <a href="{{ route('admin.previewMail', $rep->id) }}" target="_blank" style="font-size: 11px; color: var(--color-primary); text-decoration: none;">
                                    <i class="fa-solid fa-eye"></i> Voir le mail envoyé
                                </a>
                            </div>
                        @elseif($rep->status === 'prete' || $rep->status === 'pret')
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <form action="{{ route('admin.reparations.notifier', $rep->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-success-glow" style="padding: 6px 12px; font-size: 12px;">
                                        <i class="fa-solid fa-paper-plane"></i> Notifier Client
                                    </button>
                                </form>
                                <a href="{{ route('admin.previewMail', $rep->id) }}" target="_blank" class="btn-outline-dark" style="padding: 6px 10px; font-size: 12px;" title="Prévisualiser avant envoi">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        @else
                            <span style="font-size: 12px; color: var(--text-muted);">
                                Déclenchable dès statut "Prête"
                            </span>
                        @endif
                    </td>

                    <td>
                        <button type="button" class="btn-outline-dark" style="padding: 6px 10px; font-size: 12px;" onclick="openEditRepModal({{ $rep->id }}, '{{ $rep->status }}', '{{ $rep->mecanique_id }}', '{{ addslashes($rep->diagnostic) }}', '{{ $rep->cout_pieces }}', '{{ $rep->cout_main_oeuvre }}')">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">
                        Aucun ordre de réparation trouvé dans cette catégorie.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $reparations->links() }}
    </div>

    <!-- Edit Status & Diagnostic Modal -->
    <div id="editRepModal" class="modal-overlay">
        <div class="modal-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 14px;">
                <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">
                    <i class="fa-solid fa-pen-to-square" style="color: var(--color-primary);"></i> Mettre à Jour l'Intervention
                </h2>
                <button type="button" onclick="closeEditRepModal()" style="background: none; border: none; color: var(--text-muted); font-size: 20px; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="editRepForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Statut de la Réparation</label>
                    <select name="status" id="repModalStatus" class="form-control-dark" required onchange="toggleEmailOption()">
                        <option value="en_attente">En attente d'attribution</option>
                        <option value="diagnostic">Diagnostic technique en cours</option>
                        <option value="en_cours">En cours de réparation</option>
                        <option value="prete">Prête pour le client (Terminée)</option>
                        <option value="terminee">Livrée & Clôturée</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Technicien Assigné</label>
                    <select name="mecanique_id" id="repModalTech" class="form-control-dark">
                        <option value="">Non assigné</option>
                        @foreach($techniciens as $t)
                            <option value="{{ $t->id }}">{{ $t->user?->full_name }} ({{ $t->specialisation }})</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Coût Pièces (€)</label>
                        <input type="number" step="0.01" name="cout_pieces" id="repModalPieces" class="form-control-dark">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Coût Main d'Œuvre (€)</label>
                        <input type="number" step="0.01" name="cout_main_oeuvre" id="repModalMO" class="form-control-dark">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Notes de Diagnostic / Compte-rendu</label>
                    <textarea name="diagnostic" id="repModalDiag" rows="3" class="form-control-dark" placeholder="Rapport technique d'intervention..."></textarea>
                </div>

                <!-- Auto Email Checkbox -->
                <div id="emailNotifyBox" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); padding: 14px; border-radius: 12px; margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #34d399; font-weight: 600; font-size: 13px;">
                        <input type="checkbox" name="notifier_client" value="1" checked style="accent-color: #10b981; width: 18px; height: 18px;">
                        <span>Envoyer automatiquement l'e-mail de notification "Véhicule Prêt" au client</span>
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" onclick="closeEditRepModal()" class="btn-outline-dark">Annuler</button>
                    <button type="submit" class="btn-primary-glow">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openEditRepModal(id, status, techId, diag, pieces, mo) {
        document.getElementById('editRepForm').action = "/admin/reparations/" + id + "/status";
        document.getElementById('repModalStatus').value = status;
        document.getElementById('repModalTech').value = techId || "";
        document.getElementById('repModalDiag').value = diag || "";
        document.getElementById('repModalPieces').value = pieces || "0.00";
        document.getElementById('repModalMO').value = mo || "0.00";
        toggleEmailOption();
        document.getElementById('editRepModal').style.display = 'flex';
    }

    function closeEditRepModal() {
        document.getElementById('editRepModal').style.display = 'none';
    }

    function toggleEmailOption() {
        const status = document.getElementById('repModalStatus').value;
        const box = document.getElementById('emailNotifyBox');
        if (status === 'prete' || status === 'pret') {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }
</script>
@endsection
