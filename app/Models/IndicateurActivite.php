<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class IndicateurActivite extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

     protected $table="t_activite_indicateur";
     protected $fillable = [
        "indicateurid",
        "activiteid",
        "status",
        "deleted"
    ];
   public function indicateur()
    {
        return $this->belongsTo(indicateur::class, 'indicateurid','id');
   }

}
