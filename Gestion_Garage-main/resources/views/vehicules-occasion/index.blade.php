@extends('layouts.app')

@section('title', 'Véhicules d’occasion')
@section('page_title', 'Véhicules d’occasion')
@section('page_subtitle', 'Une sélection de véhicules contrôlés et disponibles à la vente')

@section('styles')
<style>
    .used-cars-intro { display:flex; align-items:flex-end; justify-content:space-between; gap:24px; margin-bottom:24px; }
    .used-cars-intro p { max-width:650px; margin:6px 0 0; color:var(--text-muted); line-height:1.65; }
    .used-cars-filter { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:22px; padding:16px; background:#fff; border:1px solid var(--border-color); border-radius:10px; }
    .used-cars-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(285px,1fr)); gap:20px; }
    .used-car-card { overflow:hidden; background:#fff; border:1px solid var(--border-color); border-radius:10px; box-shadow:var(--shadow-card); }
    .used-car-card img { display:block; width:100%; height:205px; object-fit:cover; background:#e2e8f0; }
    .used-car-body { padding:18px; }
    .used-car-ref { color:var(--text-muted); font-size:11px; letter-spacing:.05em; }
    .used-car-title { margin:5px 0 12px; color:var(--text-primary); font-size:18px; font-weight:700; }
    .used-car-specs { display:flex; flex-wrap:wrap; gap:7px; margin:0 0 14px; }
    .used-car-specs span { padding:5px 8px; color:var(--text-secondary); background:#f8fafc; border:1px solid var(--border-color); border-radius:5px; font-size:11px; }
    .used-car-description { min-height:40px; margin:0 0 16px; color:var(--text-muted); font-size:13px; line-height:1.55; }
    .used-car-footer { display:flex; align-items:center; justify-content:space-between; gap:12px; padding-top:14px; border-top:1px solid var(--border-color); }
    .used-car-price { color:#0f172a; font-size:21px; font-weight:700; }
    .used-car-contact { display:inline-flex; align-items:center; justify-content:center; padding:9px 12px; color:#fff; background:#1d4ed8; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none; }
    .used-car-contact:hover { color:#fff; background:#1e40af; }
    .used-car-price-form { display:flex; align-items:center; gap:8px; margin-top:12px; }
    .used-car-price-form input { min-width:0; width:100%; padding:9px 10px; }
    .used-car-price-form button { flex:none; padding:9px 12px; }
    @media(max-width:700px) { .used-cars-intro { align-items:flex-start; flex-direction:column; } }
</style>
@endsection

@section('content')
<section class="used-cars-intro">
    <div>
        <h2 style="margin:0;color:var(--text-primary);font-size:22px;font-weight:700;">Notre sélection automobile</h2>
        <p>Chaque véhicule est présenté avec son kilométrage, sa motorisation et son prix. Contactez le garage pour organiser une visite ou obtenir le dossier d’entretien.</p>
    </div>
    <div style="color:var(--text-muted);font-size:13px;">{{ $vehicules->total() }} véhicule(s) disponible(s)</div>
</section>

<form class="used-cars-filter" method="GET" action="{{ route('vehicules-occasion.index') }}">
    <input class="form-control-dark" style="max-width:320px;" type="search" name="recherche" value="{{ request('recherche') }}" placeholder="Marque, modèle ou référence">
    <input class="form-control-dark" style="max-width:240px;" type="number" min="0" step="100000" name="prix_max" value="{{ request('prix_max') }}" placeholder="Budget maximum (FCFA)">
    <button class="btn-primary-glow" type="submit" style="padding:9px 16px;">Rechercher</button>
    @if(request()->hasAny(['recherche', 'prix_max']))
        <a href="{{ route('vehicules-occasion.index') }}" style="align-self:center;color:var(--text-muted);font-size:13px;">Effacer les filtres</a>
    @endif
</form>

<div class="used-cars-grid">
    @forelse($vehicules as $vehicule)
        <article class="used-car-card">
            <img src="{{ $vehicule->image_url }}" alt="{{ $vehicule->marque }} {{ $vehicule->modele }}" loading="lazy">
            <div class="used-car-body">
                <div class="used-car-ref">{{ $vehicule->reference }} · {{ $vehicule->annee }}</div>
                <h3 class="used-car-title">{{ $vehicule->marque }} {{ $vehicule->modele }}</h3>
                <div class="used-car-specs">
                    <span>{{ number_format($vehicule->kilometrage, 0, ',', ' ') }} km</span>
                    <span>{{ $vehicule->carburant }}</span>
                    <span>{{ $vehicule->boite }}</span>
                </div>
                <p class="used-car-description">{{ $vehicule->description }}</p>
                <div class="used-car-footer">
                    <strong class="used-car-price">{{ number_format((float) $vehicule->prix, 0, ',', ' ') }} FCFA</strong>
                    @if(!Auth::user()->isAdmin())
                        <a class="used-car-contact" href="mailto:{{ config('mail.from.address') }}?subject={{ rawurlencode('Demande d’information - ' . $vehicule->marque . ' ' . $vehicule->modele . ' (' . $vehicule->reference . ')') }}">Contacter le garage</a>
                    @endif
                </div>
                @if(Auth::user()->isAdmin())
                    <form class="used-car-price-form" method="POST" action="{{ route('admin.vehicules-occasion.price', $vehicule) }}">
                        @csrf
                        @method('PUT')
                        <input class="form-control-dark" type="number" name="prix" min="1" max="{{ (int) $vehicule->prix - 1 }}" step="1" value="{{ old('prix') }}" placeholder="Nouveau prix en FCFA" aria-label="Nouveau prix en FCFA pour {{ $vehicule->marque }} {{ $vehicule->modele }}" required>
                        <button class="btn-primary-glow" type="submit">Réduire le prix</button>
                    </form>
                @endif
            </div>
        </article>
    @empty
        <div style="grid-column:1/-1;padding:36px;text-align:center;background:#fff;border:1px solid var(--border-color);border-radius:10px;color:var(--text-muted);">
            Aucun véhicule ne correspond à ces critères.
        </div>
    @endforelse
</div>

<div style="margin-top:22px;">{{ $vehicules->links() }}</div>
@endsection
