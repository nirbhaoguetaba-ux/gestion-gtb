<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Vehicule extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'brand',
        'model',
        'annee',
        'fuel_type',
        'registration_number',
        'vin',
        'kilometrage',
        'boite_vitesse',
        'couleur',
        'statut',
        'photo',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function reparations()
    {
        return $this->hasMany(Reparation::class);
    }

    public function activeReparation()
    {
        return $this->hasOne(Reparation::class)->latestOfMany();
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }
            if (file_exists(public_path($this->photo))) {
                return asset($this->photo);
            }
            if (Storage::disk('public')->exists($this->photo)) {
                return asset('storage/' . $this->photo);
            }
        }

        // Vraies photographies de haute qualité de véhicules réels
        $brand = strtolower($this->brand ?? '');
        $model = strtolower($this->model ?? '');

        if (str_contains($brand, 'peugeot') || str_contains($model, '208')) {
            return 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80';
        }
        if (str_contains($brand, 'bmw') || str_contains($model, 'série 3') || str_contains($model, 'serie 3')) {
            return 'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=800&q=80';
        }
        if (str_contains($brand, 'mercedes') || str_contains($model, 'classe a')) {
            return 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?auto=format&fit=crop&w=800&q=80';
        }
        if (str_contains($brand, 'tesla') || str_contains($model, 'model 3')) {
            return 'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=800&q=80';
        }
        if (str_contains($brand, 'renault') || str_contains($model, 'clio')) {
            return 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80';
        }
        if (str_contains($brand, 'audi') || str_contains($model, 'a4')) {
            return 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?auto=format&fit=crop&w=800&q=80';
        }

        return 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80';
    }

    public function getStatutBadgeAttribute(): array
    {
        return match ($this->statut) {
            'pret', 'prete' => ['label' => 'Prêt à récupérer', 'class' => 'bg-emerald-500 text-white', 'icon' => 'fa-check-circle'],
            'en_reparation', 'en_cours' => ['label' => 'En Réparation', 'class' => 'bg-amber-500 text-white', 'icon' => 'fa-wrench'],
            'diagnostic' => ['label' => 'Diagnostic en cours', 'class' => 'bg-blue-500 text-white', 'icon' => 'fa-search'],
            'livre' => ['label' => 'Restitué au client', 'class' => 'bg-slate-500 text-white', 'icon' => 'fa-check-double'],
            default => ['label' => 'En attente prise en charge', 'class' => 'bg-indigo-500 text-white', 'icon' => 'fa-clock'],
        };
    }
}
