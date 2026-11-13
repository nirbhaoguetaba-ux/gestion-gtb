<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class Facture extends Model
{
    use HasFactory;
    protected $fillable = [
        'repair_id', 'date', 'total_amount',
    ];
    public function setDateAttribute($value)
    {
        if ($value instanceof \DateTimeInterface) {
            $this->attributes['date'] = $value->format('Y-m-d H:i:s');
        } elseif (is_string($value)) {
            $this->attributes['date'] = (new DateTime($value))->format('Y-m-d H:i:s');
        } else {
            $this->attributes['date'] = $value;
        }
    }
    public function repair()
    {
        return $this->belongsTo(Reparation::class);
    }
}
