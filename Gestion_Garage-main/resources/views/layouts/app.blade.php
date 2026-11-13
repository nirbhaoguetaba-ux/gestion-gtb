<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion d\'Atelier Automobile') &bull; Mécano D-CLIC</title>

    <!-- Icones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Feuille de style professionnelle -->
    <link rel="stylesheet" href="{{ asset('assets/css/modern_garage.css') }}?v=responsive-20261014-2">
    @yield('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Barre Latérale -->
        <aside class="app-sidebar">
            <div class="brand-header">
                <div class="brand-logo-icon">
                    <img src="{{ asset('images/gle-63.jpg') }}" alt="Mercedes-AMG GLE 63 S 4MATIC">
                </div>
                <div class="brand-text">
                    <h2>Mécano D-CLIC</h2>
                    <span>Atelier · {{ config('app.garage_address') }}</span>
                </div>
            </div>

            <!-- Profil de l'utilisateur actif -->
            @auth
            <div class="sidebar-user">
                <x-profile-initials :user="Auth::user()" size="36" />
                <div class="user-info">
                    <div class="user-name">
                        {{ Auth::user()->isAdmin() ? Auth::user()->Nom . ' ' . Auth::user()->Prenom : Auth::user()->full_name }}
                    </div>
                    @if(Auth::user()->isAdmin())
                        <span class="user-badge badge-role-admin">Administrateur</span>
                    @elseif(Auth::user()->isTechnicien())
                        <span class="user-badge badge-role-tech">Technicien</span>
                    @else
                        <span class="user-badge badge-role-client">Client</span>
                    @endif
                </div>
            </div>
            @endauth

            <!-- Menu de Navigation -->
            <nav class="nav-menu">
                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-line"></i>
                            <span>Tableau de bord</span>
                        </a>
                        <a href="{{ route('admin.reparations') }}" class="nav-link-item {{ request()->routeIs('admin.reparations*') ? 'active' : '' }}">
                            <i class="fa-solid fa-clipboard-list"></i>
                            <span>Ordres de réparation</span>
                        </a>
                        <a href="{{ route('admin.techniciens') }}" class="nav-link-item {{ request()->routeIs('admin.techniciens*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-gear"></i>
                            <span>Techniciens</span>
                        </a>
                        <a href="{{ route('admin.clients') }}" class="nav-link-item {{ request()->routeIs('admin.clients*') ? 'active' : '' }}">
                            <i class="fa-solid fa-users"></i>
                            <span>Clients</span>
                        </a>
                        <a href="{{ route('vehicules-occasion.index') }}" class="nav-link-item {{ request()->routeIs('vehicules-occasion.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-tags"></i>
                            <span>Véhicules d’occasion</span>
                        </a>
                    @elseif(Auth::user()->isTechnicien())
                        <a href="{{ route('technicien.dashboard') }}" class="nav-link-item active">
                            <i class="fa-solid fa-wrench"></i>
                            <span>Fiches d'intervention</span>
                        </a>
                        <a href="{{ route('vehicules-occasion.index') }}" class="nav-link-item {{ request()->routeIs('vehicules-occasion.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-tags"></i>
                            <span>Véhicules d’occasion</span>
                        </a>
                    @else
                        <a href="{{ route('client.dashboard') }}" class="nav-link-item active">
                            <i class="fa-solid fa-car"></i>
                            <span>Mes véhicules et suivi</span>
                        </a>
                        <a href="{{ route('vehicules-occasion.index') }}" class="nav-link-item {{ request()->routeIs('vehicules-occasion.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-tags"></i>
                            <span>Véhicules d’occasion</span>
                        </a>
                    @endif
                @endauth
            </nav>

            <!-- Déconnexion -->
            <div style="border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: 14px; margin-top: auto;">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link-item" style="width:100%;border:0;background:transparent;text-align:left;color:#f87171;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Contenu Principal -->
        <main class="app-main">
            <!-- Barre de navigation supérieure -->
            <header class="top-navbar">
                <div class="top-title">
                    <h1>@yield('page_title', 'Tableau de bord')</h1>
                    <p>@yield('page_subtitle', 'Système de gestion d\'atelier mécanique automobile')</p>
                </div>

                <div class="top-actions">
                    @yield('top_buttons')
                </div>
            </header>
            <div style="margin:-14px 0 22px;color:var(--text-muted);font-size:12px;">
                <i class="fa-solid fa-location-dot" style="margin-right:5px;color:var(--color-primary);"></i>
                Atelier automobile · {{ config('app.garage_address') }}
            </div>

            <!-- Messages d'information -->
            @if(session('success'))
                <div class="alert-box alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-box alert-rose">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert-box alert-rose">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
