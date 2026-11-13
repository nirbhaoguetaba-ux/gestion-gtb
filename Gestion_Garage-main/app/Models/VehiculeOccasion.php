<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehiculeOccasion extends Model
{
    protected $table = 'vehicules_occasion';

    protected $fillable = [
        'reference',
        'marque',
        'modele',
        'annee',
        'kilometrage',
        'carburant',
        'boite',
        'prix',
        'image_url',
        'description',
        'statut',
    ];

    protected $casts = [
        'annee' => 'integer',
        'kilometrage' => 'integer',
        'prix' => 'decimal:2',
    ];
}
