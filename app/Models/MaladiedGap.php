<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MaladiedGap extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

     protected $table="t_maladie_gap";

     protected $fillable = [
        'gapid',
        "maladieid",
        "nbrCas",
        "nbrDeces",
        "status",
        "deleted"
    ];

    public function maladie()
    {
        return $this->belongsTo(Maladie::class, 'maladieid','id');
    }

}
