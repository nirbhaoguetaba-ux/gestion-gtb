<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion &bull; Mécano D-CLIC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/modern_garage.css') }}?v=responsive-20261014-2">
    <style>
        body.login-page {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(350px, 440px);
            align-items: center;
            gap: clamp(36px, 8vw, 120px);
            min-height: 100vh;
            padding: clamp(24px, 7vw, 100px);
            color: #fff;
            background:
                linear-gradient(90deg, rgba(8, 15, 29, .57) 0%, rgba(8, 15, 29, .40) 46%, rgba(8, 15, 29, .23) 100%),
                var(--workshop-image) center / cover no-repeat;
        }
        .login-story {
            max-width: 650px;
            padding: 16px 0;
        }
        .login-story-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
            color: #bfdbfe;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .login-story-kicker::before {
            width: 30px;
            height: 2px;
            background: #60a5fa;
            content: "";
        }
        .login-story h2 {
            max-width: 610px;
            margin: 0 0 18px;
            font-size: clamp(32px, 4.5vw, 54px);
            font-weight: 650;
            line-height: 1.12;
            letter-spacing: -.025em;
            text-shadow: 0 2px 18px rgba(0, 0, 0, .2);
        }
        .login-story p {
            max-width: 500px;
            margin: 0;
            color: rgba(255,255,255,.78);
            font-size: 16px;
            line-height: 1.75;
        }
        .login-box {
            width: 100%;
            max-width: 440px;
            justify-self: end;
            background: #ffffff;
            border: 1px solid rgba(255,255,255,.55);
            border-radius: 6px;
            padding: clamp(26px, 4vw, 38px);
            box-shadow: 0 24px 70px rgba(0, 0, 0, .28);
        }
        .brand-icon-box {
            width: 64px;
            height: 44px;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 12px;
        }
        .brand-icon-box img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .login-location {
            margin-top: 18px;
            color: #64748b;
            font-size: 12px;
        }
        @media (max-width: 760px) {
            body.login-page {
                grid-template-columns: 1fr;
                justify-items: center;
                gap: 22px;
                padding: 24px 16px;
                background-position: center;
            }
            .login-story {
                width: min(100%, 440px);
                padding: 4px 0 0;
            }
            .login-story h2 {
                font-size: clamp(30px, 8vw, 42px);
            }
            .login-story p {
                font-size: 14px;
            }
            .login-box {
                justify-self: center;
            }
        }
    </style>
</head>
<body class="login-page" style="--workshop-image: url('{{ asset('images/atelier-mecanique.jpg') }}')">
    <section class="login-story" aria-label="Présentation du garage">
        <div class="login-story-kicker">Atelier automobile · {{ config('app.garage_address') }}</div>
        <h2>Le savoir-faire de l’atelier, au service de votre véhicule.</h2>
        <p>Suivi des réparations, entretien et accompagnement automobile à Lomé. Connectez-vous à votre espace pour retrouver les informations liées à votre compte.</p>
    </section>
    <div class="login-box">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-icon-box">
                <img src="{{ asset('images/gle-63.jpg') }}" alt="Mercedes-AMG GLE 63 S 4MATIC">
            </div>
            <h1 style="font-size: 21px; font-weight: 650; color: #202c30; margin-bottom: 4px;">Mécano D-CLIC</h1>
            <p style="font-size: 13px; color: #64748b; margin: 0;">Gestion d'atelier mécanique et suivi des réparations</p>
            <div class="login-location">{{ config('app.garage_address') }}</div>
        </div>

        @if(session('error'))
            <div class="alert-box alert-rose" style="margin-bottom: 16px;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert-box alert-rose" style="margin-bottom: 16px;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" id="loginForm">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" class="form-control-dark" placeholder="adresse@exemple.fr" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label class="form-label" for="password" style="margin-bottom: 0;">Mot de passe</label>
                </div>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" class="form-control-dark" style="padding-right: 36px;" required>
                    <button type="button" onclick="togglePassword()" style="position: absolute; right: 10px; top: 10px; background: none; border: none; color: #64748b; cursor: pointer;">
                        <i class="fa-regular fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 13px;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: #475569;">
                    <input type="checkbox" name="remember_me"> Mémoriser ma session
                </label>
            </div>

            <button type="submit" class="btn-primary-glow" style="width: 100%; padding: 11px; font-size: 14px;">
                Connexion
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 13px; color: #64748b;">
            Nouveau client ? <a href="{{ route('register') }}" style="color: #1d4ed8; font-weight: 500; text-decoration: none;">Créer un compte</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passInput = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
