#!/bin/bash
set -e

# Port dynamique fourni par Render
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/:80/:$PORT/" /etc/apache2/sites-available/*.conf

# Initialisation SQLite automatique si activé
if [ "$DB_CONNECTION" = "sqlite" ] && [ ! -f "/var/www/html/database/database.sqlite" ]; then
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

# Migrations et caches Laravel
php artisan config:clear || true
php artisan migrate --force || true
php artisan db:seed --force || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec apache2-foreground
