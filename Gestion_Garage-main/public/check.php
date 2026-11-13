<?php
/**
 * Diagnostic & Configuration Checker for Laravel Hosting
 * Garage Mécano D-CLIC - PlanetHoster N0C / LiteSpeed Compatibility Check
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

$checks = [];

// 1. PHP Version
$phpVersion = PHP_VERSION;
$phpVersionId = PHP_VERSION_ID;
$phpOk = ($phpVersionId >= 80100 && $phpVersionId < 80400);
$checks[] = [
    'title' => 'Version PHP',
    'status' => $phpOk,
    'detail' => "Version détectée : PHP $phpVersion (Requis : PHP 8.1.x, 8.2.x ou 8.3.x).",
    'help' => $phpOk ? '' : 'Dans votre panneau N0C PlanetHoster > Gestion des Langages > PHP : sélectionnez PHP 8.2 ou 8.3 pour ce domaine.'
];

// 2. Extensions PHP requises
$requiredExtensions = [
    'pdo' => 'PDO',
    'pdo_mysql' => 'PDO MySQL (Connexion Base de données)',
    'mbstring' => 'Mbstring (Gestion chaînes multioctets)',
    'openssl' => 'OpenSSL (Chiffrement / Sécurité)',
    'fileinfo' => 'FileInfo (Upload de fichiers / images)',
    'tokenizer' => 'Tokenizer (Compilation Blade)',
    'xml' => 'XML / DOM (Génération PDF / Excel)',
    'ctype' => 'Ctype',
    'json' => 'JSON',
    'bcmath' => 'BCMath (Calculs haute précision)',
    'gd' => 'GD (Traitement des photos de véhicules)',
    'zip' => 'Zip (Export / Import Excel)'
];

$missingExts = [];
foreach ($requiredExtensions as $ext => $label) {
    if (!extension_loaded($ext)) {
        $missingExts[] = "$label ($ext)";
    }
}
$checks[] = [
    'title' => 'Extensions PHP requises',
    'status' => empty($missingExts),
    'detail' => empty($missingExts) ? 'Toutes les extensions nécessaires sont activées.' : 'Extensions manquantes : ' . implode(', ', $missingExts),
    'help' => empty($missingExts) ? '' : 'Activez ces extensions dans N0C > Langages > PHP > Extensions.'
];

// 3. Fichier vendor/autoload.php
$baseDir = dirname(__DIR__);
$vendorAutoload = $baseDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$vendorOk = file_exists($vendorAutoload);
$checks[] = [
    'title' => 'Dépendances Composer (vendor)',
    'status' => $vendorOk,
    'detail' => $vendorOk ? "Le fichier vendor/autoload.php est bien présent ($vendorAutoload)." : "Dossier 'vendor' manquant ou incomplet dans $baseDir !",
    'help' => $vendorOk ? '' : 'Exécutez `composer install --no-dev --optimize-autoloader` en SSH ou téléversez le dossier `vendor` complet via FTP / Gestionnaire de fichiers N0C.'
];

// 4. Fichier d'environnement .env
$envFile = $baseDir . DIRECTORY_SEPARATOR . '.env';
$envOk = file_exists($envFile);
$appKeySet = false;
$appDebug = false;
$dbHost = '';
$dbName = '';
$dbUser = '';
$dbPass = '';

if ($envOk) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#')) continue;
        if (str_starts_with($line, 'APP_KEY=')) {
            $key = trim(substr($line, 8));
            $appKeySet = !empty($key) && $key !== 'null';
        }
        if (str_starts_with($line, 'APP_DEBUG=')) {
            $appDebug = strtolower(trim(substr($line, 10))) === 'true';
        }
        if (str_starts_with($line, 'DB_HOST=')) $dbHost = trim(substr($line, 8));
        if (str_starts_with($line, 'DB_DATABASE=')) $dbName = trim(substr($line, 12));
        if (str_starts_with($line, 'DB_USERNAME=')) $dbUser = trim(substr($line, 12));
        if (str_starts_with($line, 'DB_PASSWORD=')) $dbPass = trim(substr($line, 12), " \t\n\r\0\x0B\"'");
    }
}

$checks[] = [
    'title' => 'Fichier de configuration .env & Clé APP_KEY',
    'status' => $envOk && $appKeySet,
    'detail' => !$envOk 
        ? "Fichier .env introuvable dans $baseDir !" 
        : (!$appKeySet ? "Fichier .env trouvé, mais APP_KEY est vide ! (Cause majeure de page blanche)" : "Fichier .env présent avec APP_KEY configurée."),
    'help' => !$envOk 
        ? 'Copiez .env.example vers .env et configurez-le.' 
        : (!$appKeySet ? 'Exécutez `php artisan key:generate` ou ajoutez une clé base64 dans APP_KEY.' : '')
];

// 5. Répertoires de stockage (storage & bootstrap/cache)
$storageDirs = [
    $baseDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views',
    $baseDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'sessions',
    $baseDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache',
    $baseDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs',
    $baseDir . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache',
];

// Auto-création si demandé ou si manquants
$storageWritable = true;
$storageDetails = [];
foreach ($storageDirs as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0775, true);
    }
    $isWritable = is_writable($dir);
    $rel = str_replace($baseDir, '', $dir);
    if (!$isWritable) {
        $storageWritable = false;
        $storageDetails[] = "$rel (Non accessible en écriture)";
    }
}

$checks[] = [
    'title' => 'Permissions des dossiers (storage & bootstrap/cache)',
    'status' => $storageWritable,
    'detail' => $storageWritable 
        ? 'Tous les dossiers de stockage et de cache sont accessibles en écriture (775/777).' 
        : 'Permissions manquantes sur : ' . implode(', ', $storageDetails),
    'help' => $storageWritable ? '' : 'Appliquez chmod -R 775 storage bootstrap/cache dans le terminal ou via le Gestionnaire de fichiers N0C.'
];

// 6. Test de connexion à la Base de données MySQL (si .env configuré)
$dbStatus = false;
$dbDetail = '';
if ($envOk && !empty($dbName) && $dbName !== 'laravel' && !empty($dbUser)) {
    try {
        $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 3
        ]);
        $dbStatus = true;
        // Vérifier si les tables existent
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $count = count($tables);
        $dbDetail = "Connexion réussie à la base '$dbName'. ($count tables trouvées" . ($count === 0 ? " - Pensez à exécuter les migrations !" : "") . ").";
    } catch (\Throwable $e) {
        $dbStatus = false;
        $dbDetail = "Erreur de connexion MySQL : " . $e->getMessage();
    }
    $checks[] = [
        'title' => 'Connexion Base de données MySQL',
        'status' => $dbStatus,
        'detail' => $dbDetail,
        'help' => $dbStatus ? '' : 'Vérifiez DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD dans votre fichier .env et les privilèges de l\'utilisateur MySQL.'
    ];
} else {
    $checks[] = [
        'title' => 'Connexion Base de données MySQL',
        'status' => false,
        'detail' => "Paramètres de base de données non configurés dans le fichier .env (DB_DATABASE='$dbName', DB_USERNAME='$dbUser').",
        'help' => 'Créez une base MySQL dans N0C > Bases de données, associez un utilisateur avec tous les privilèges, et renseignez les identifiants dans .env.'
    ];
}

$allOk = true;
foreach ($checks as $c) {
    if (!$c['status']) {
        $allOk = false;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic Hébergement &bull; Garage Mécano D-CLIC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern_garage.css">
    <style>
        body {
            background: #0f172a;
            color: #f8fafc;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 30px 20px;
            margin: 0;
        }
        .container {
            max-width: 860px;
            margin: 0 auto;
            background: #1e293b;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            border: 1px solid #334155;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #334155;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #38bdf8;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .status-badge {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-badge.ok {
            background: #065f46;
            color: #34d399;
        }
        .status-badge.ko {
            background: #7f1d1d;
            color: #f87171;
        }
        .check-card {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        .check-card.ok {
            border-left: 4px solid #10b981;
        }
        .check-card.ko {
            border-left: 4px solid #ef4444;
        }
        .check-icon {
            font-size: 22px;
            margin-top: 2px;
        }
        .check-icon.ok { color: #10b981; }
        .check-icon.ko { color: #ef4444; }
        .check-content { flex: 1; }
        .check-title {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 6px 0;
            color: #f1f5f9;
        }
        .check-detail {
            font-size: 14px;
            color: #94a3b8;
            margin: 0;
            line-height: 1.5;
        }
        .check-help {
            margin-top: 8px;
            font-size: 13px;
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.1);
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .actions {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #334155;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #2563eb;
            color: #fff;
        }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary {
            background: #334155;
            color: #cbd5e1;
        }
        .btn-secondary:hover { background: #475569; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><i class="fa-solid fa-wrench"></i> Diagnostic Hébergement &bull; Garage Mécano</h1>
        <span class="status-badge <?= $allOk ? 'ok' : 'ko' ?>">
            <?= $allOk ? '<i class="fa-solid fa-circle-check"></i> Prêt pour exécution' : '<i class="fa-solid fa-circle-exclamation"></i> Configuration requise' ?>
        </span>
    </div>

    <div class="checks-list">
        <?php foreach ($checks as $c): ?>
            <div class="check-card <?= $c['status'] ? 'ok' : 'ko' ?>">
                <div class="check-icon <?= $c['status'] ? 'ok' : 'ko' ?>">
                    <i class="fa-solid <?= $c['status'] ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i>
                </div>
                <div class="check-content">
                    <h3 class="check-title"><?= htmlspecialchars($c['title']) ?></h3>
                    <p class="check-detail"><?= htmlspecialchars($c['detail']) ?></p>
                    <?php if (!empty($c['help'])): ?>
                        <div class="check-help">
                            <i class="fa-solid fa-lightbulb"></i> <strong>Solution :</strong> <?= htmlspecialchars($c['help']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="actions">
        <a href="index.php" class="btn btn-primary"><i class="fa-solid fa-arrow-right"></i> Accéder au site principal</a>
        <a href="check.php" class="btn btn-secondary"><i class="fa-solid fa-rotate"></i> Relancer le diagnostic</a>
    </div>
</div>
</body>
</html>

