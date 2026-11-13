<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reparation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicule_id',
        'mecanique_id',
        'titre',
        'status',
        'priorite',
        'description',
        'diagnostic',
        'cout_pieces',
        'cout_main_oeuvre',
        'cout_total',
        'notification_envoyee',
        'notification_envoyee_at',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'cout_pieces' => 'decimal:2',
        'cout_main_oeuvre' => 'decimal:2',
        'cout_total' => 'decimal:2',
        'notification_envoyee' => 'boolean',
        'notification_envoyee_at' => 'datetime',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function mecanique()
    {
        return $this->belongsTo(Mecanique::class);
    }

    public function facture()
    {
        return $this->hasOne(Facture::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'prete', 'pret' => ['label' => 'Prête pour le client', 'badge_class' => 'badge-success', 'color' => '#10b981'],
            'terminee' => ['label' => 'Terminée & Facturée', 'badge_class' => 'badge-primary', 'color' => '#3b82f6'],
            'en_cours' => ['label' => 'En cours d\'intervention', 'badge_class' => 'badge-warning', 'color' => '#f59e0b'],
            'diagnostic' => ['label' => 'Diagnostic technique', 'badge_class' => 'badge-info', 'color' => '#06b6d4'],
            default => ['label' => 'En attente d\'attribution', 'badge_class' => 'badge-secondary', 'color' => '#64748b'],
        };
    }
}
