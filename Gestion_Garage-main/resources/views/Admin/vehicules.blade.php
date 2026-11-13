@extends('layouts.app')

@section('title', 'Parc de véhicules &bull; Mécano D-CLIC')
@section('page_title', 'Parc de véhicules & Fiches techniques')
@section('page_subtitle', 'Caractéristiques, motorisations, immatriculations et statut atelier')

@section('top_buttons')
    <button type="button" class="btn-primary-glow" onclick="openAddModal()">
        <i class="fa-solid fa-plus"></i> Ajouter un véhicule
    </button>
@endsection

@section('content')
    <!-- Barre de recherche et de filtres -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; padding: 14px 18px; margin-bottom: 24px;">
        <form method="GET" action="{{ route('admin.vehicules') }}" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control-dark" placeholder="Rechercher par marque, modèle, immatriculation ou VIN...">
            </div>

            <div style="min-width: 150px;">
                <select name="carburant" class="form-control-dark">
                    <option value="">Tous carburants</option>
                    <option value="Essence" {{ request('carburant') == 'Essence' ? 'selected' : '' }}>Essence</option>
                    <option value="Diesel" {{ request('carburant') == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                    <option value="Électrique" {{ request('carburant') == 'Électrique' ? 'selected' : '' }}>Électrique</option>
                    <option value="Hybride" {{ request('carburant') == 'Hybride' ? 'selected' : '' }}>Hybride</option>
                </select>
            </div>

            <div style="min-width: 150px;">
                <select name="statut" class="form-control-dark">
                    <option value="">Tous statuts</option>
                    <option value="pret" {{ request('statut') == 'pret' ? 'selected' : '' }}>Prêt pour restitution</option>
                    <option value="en_reparation" {{ request('statut') == 'en_reparation' ? 'selected' : '' }}>En atelier</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                </select>
            </div>

            <button type="submit" class="btn-primary-glow" style="padding: 9px 16px;">
                Filtrer
            </button>
            @if(request()->hasAny(['search', 'carburant', 'statut']))
                <a href="{{ route('admin.vehicules') }}" class="btn-outline-dark" style="padding: 9px 14px;">
                    Réinitialiser
                </a>
            @endif
        </form>
    </div>

    <!-- Grille des fiches de véhicules réels -->
    <div class="vehicle-grid">
        @forelse($vehicules as $v)
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
                        <span class="spec-label">Boîte</span>
                        <span class="spec-value">{{ $v->boite_vitesse }}</span>
                    </div>
                    <div class="spec-item" style="grid-column: span 2;">
                        <span class="spec-label">Numéro VIN</span>
                        <span class="spec-value" style="font-family: monospace; font-size: 11px; color: #475569;">
                            {{ $v->vin ?? 'Non renseigné' }}
                        </span>
                    </div>
                </div>

                <div class="vehicle-card-footer">
                    <a href="{{ route('admin.vehicules.show', $v->id) }}" class="btn-outline-dark" style="flex: 1; padding: 7px 10px; font-size: 12px;">
                        Fiche complète
                    </a>
                    
                    <form action="{{ route('admin.vehicules.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression de ce véhicule ?');" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-outline-dark" style="color: #dc2626; padding: 7px 10px;" title="Supprimer">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: #ffffff; border-radius: 8px; border: 1px solid var(--border-color);">
            <div style="font-size: 15px; font-weight: 600; color: #0f172a; margin-bottom: 4px;">Aucun véhicule trouvé</div>
            <div style="font-size: 13px; color: #64748b;">Modifiez vos critères de recherche ou enregistrez un véhicule.</div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top: 24px;">
        {{ $vehicules->links() }}
    </div>

    <!-- Modale Ajouter un Véhicule -->
    <div id="addVehicleModal" class="modal-overlay">
        <div class="modal-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <h2 style="font-size: 17px; font-weight: 700; color: #0f172a;">
                    Enregistrer un nouveau véhicule
                </h2>
                <button type="button" onclick="closeAddModal()" style="background: none; border: none; color: #64748b; font-size: 18px; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.vehicules.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Client propriétaire</label>
                        <select name="client_id" class="form-control-dark" required>
                            <option value="">Sélectionner le client...</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->user?->full_name }} ({{ $c->user?->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label" for="vehicle-photo">Photo du véhicule</label>
                        <input type="file" id="vehicle-photo" name="photo" class="form-control-dark" accept="image/jpeg,image/png,image/webp,image/gif">
                        <small style="display: block; margin-top: 6px; color: #64748b;">Formats acceptés : JPG, PNG, WEBP ou GIF (2 Mo maximum).</small>
                        <img id="vehicle-photo-preview" alt="Aperçu de la photo du véhicule" style="display: none; width: 100%; max-height: 180px; margin-top: 10px; border-radius: 6px; object-fit: cover;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Marque</label>
                        <input type="text" name="brand" class="form-control-dark" placeholder="ex: Peugeot, BMW, Renault" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Modèle</label>
                        <input type="text" name="model" class="form-control-dark" placeholder="ex: 208, Série 3, Clio V" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Immatriculation</label>
                        <input type="text" name="registration_number" class="form-control-dark" placeholder="ex: AB-123-CD" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Année de fabrication</label>
                        <input type="number" name="annee" class="form-control-dark" value="2022" min="1990" max="2026" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Carburant</label>
                        <select name="fuel_type" class="form-control-dark" required>
                            <option value="Essence">Essence</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Électrique">Électrique</option>
                            <option value="Hybride">Hybride</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kilométrage (km)</label>
                        <input type="number" name="kilometrage" class="form-control-dark" placeholder="ex: 45000" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Boîte de vitesse</label>
                        <select name="boite_vitesse" class="form-control-dark" required>
                            <option value="Manuelle">Manuelle</option>
                            <option value="Automatique">Automatique</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Couleur</label>
                        <input type="text" name="couleur" class="form-control-dark" placeholder="ex: Gris Métallisé, Blanc" required>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Numéro de châssis (VIN)</label>
                        <input type="text" name="vin" class="form-control-dark" placeholder="ex: VF3CCHNZ6ET102938">
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Statut initial</label>
                        <select name="statut" class="form-control-dark" required>
                            <option value="en_attente">En attente</option>
                            <option value="en_reparation">En réparation</option>
                            <option value="pret">Prêt</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; border-top: 1px solid var(--border-color); padding-top: 14px;">
                    <button type="button" onclick="closeAddModal()" class="btn-outline-dark">Annuler</button>
                    <button type="submit" class="btn-primary-glow">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const vehiclePhotoInput = document.getElementById('vehicle-photo');
    const vehiclePhotoPreview = document.getElementById('vehicle-photo-preview');

    vehiclePhotoInput.addEventListener('change', function() {
        const [photo] = this.files;
        if (!photo) {
            vehiclePhotoPreview.removeAttribute('src');
            vehiclePhotoPreview.style.display = 'none';
            return;
        }

        vehiclePhotoPreview.src = URL.createObjectURL(photo);
        vehiclePhotoPreview.style.display = 'block';
    });

    function openAddModal() {
        document.getElementById('addVehicleModal').style.display = 'flex';
    }
    function closeAddModal() {
        document.getElementById('addVehicleModal').style.display = 'none';
    }
    window.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash === '#addVehicleModal') {
            openAddModal();
        }
    });
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('addVehicleModal');
        if (e.target === modal) {
            closeAddModal();
        }
    });
</script>
@endsection
