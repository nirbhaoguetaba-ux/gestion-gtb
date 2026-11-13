<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte &bull; Mécano D-CLIC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/modern_garage.css') }}?v=responsive-20261014-2">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f1f5f9;
            padding: 24px;
        }
        .register-card {
            width: 100%;
            max-width: 520px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 36px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }
        .brand-icon-box {
            width: 44px;
            height: 44px;
            background: #1d4ed8;
            color: #ffffff;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 12px;
        }
    </style>
</head>
<body class="register-page">
    <div class="register-card">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-icon-box">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Créer un compte client</h1>
            <p style="font-size: 13px; color: #64748b; margin: 0;">Suivez l'état de vos véhicules et vos réparations en temps réel</p>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="alert-box alert-rose" style="margin-bottom: 18px;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="register-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Nom de famille</label>
                    <input type="text" name="Nom" class="form-control-dark" placeholder="Dupont" value="{{ old('Nom') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="Prenom" class="form-control-dark" placeholder="Marie" value="{{ old('Prenom') }}" required>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Adresse e-mail</label>
                    <input type="email" name="email" class="form-control-dark" placeholder="marie.dupont@exemple.fr" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Numéro de téléphone</label>
                    <input type="text" name="NumeroTelephone" class="form-control-dark" placeholder="+228 90 00 00 00" value="{{ old('NumeroTelephone') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control-dark" placeholder="Minimum 10 caractères" required minlength="10">
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control-dark" required minlength="10">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Adresse postale</label>
                    <input type="text" name="adresse" class="form-control-dark" placeholder="10 Rue de la Paix, 75001 Paris" value="{{ old('adresse') }}" required>
                </div>
            </div>

            <button type="submit" class="btn-primary-glow" style="width: 100%; padding: 11px; font-size: 14px; margin-top: 6px;">
                Créer mon compte
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 13px; color: #64748b;">
            Vous possédez déjà un compte ? <a href="{{ route('login') }}" style="color: #1d4ed8; font-weight: 500; text-decoration: none;">Se connecter</a>
        </div>
    </div>
</body>
</html>
