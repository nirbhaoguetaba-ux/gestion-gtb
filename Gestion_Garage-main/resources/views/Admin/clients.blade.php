@extends('layouts.app')

@section('title', 'Gestion des Clients &bull; Mécano D-CLIC')
@section('page_title', 'Répertoire des Clients')
@section('page_subtitle', 'Coordonnées, véhicules rattachés et historique des interventions')

@section('content')
    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Coordonnées</th>
                    <th>Adresse</th>
                    <th>Véhicules Enregistrés</th>
                    <th>Date d'inscription</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $c)
                @php $u = $c->user; @endphp
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            @if($u)
                                <x-profile-initials :user="$u" size="38" />
                            @endif
                            <div>
                                <div style="font-weight: 700; color: var(--text-primary); font-size: 13px;">{{ $u?->full_name }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">Client #{{ $c->id }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div style="color: var(--text-secondary); font-weight: 500; font-size: 13px;">
                            <i class="fa-regular fa-envelope" style="color: var(--color-primary); width: 14px;"></i> {{ $u?->email }}
                        </div>
                        <div style="color: var(--text-muted); font-size: 12px; margin-top: 2px;">
                            <i class="fa-solid fa-phone" style="color: var(--color-success); width: 14px;"></i> {{ $u?->NumeroTelephone ?? 'N/A' }}
                        </div>
                    </td>

                    <td>
                        <div style="color: var(--text-secondary); font-size: 13px;">
                            {{ $u?->Adresse ?? 'Adresse non renseignée' }}
                        </div>
                    </td>

                    <td>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            @forelse($c->vehicules as $v)
                                <div style="font-size: 12px;">
                                    <span style="color: var(--text-primary); font-weight: 600;">{{ $v->brand }} {{ $v->model }}</span>
                                    <span style="font-family: monospace; font-size: 10px; font-weight: 600; color: var(--color-primary); background: #eff6ff; border: 1px solid #bfdbfe; padding: 1px 6px; border-radius: 4px;">
                                        {{ $v->registration_number }}
                                    </span>
                                </div>
                            @empty
                                <span style="font-size: 12px; color: var(--text-muted); font-style: italic;">Aucun véhicule</span>
                            @endforelse
                        </div>
                    </td>

                    <td style="color: var(--text-muted); font-size: 12px; font-family: monospace;">
                        {{ $c->created_at->format('d/m/Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
                        Aucun client enregistré.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $clients->links() }}
    </div>
@endsection
