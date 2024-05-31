<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class IndicateurProjetModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;
    protected $table = "t_reponse_indicateur_projet";
    protected $filable = [
        'projetid',
        'typeimpactid',
        'id'
    ];

    public function indicateurs()
    {
        return $this->belongsToMany(indicateur::class, 't_typeimpactprojet_indicateurs', 'indicateurid', 'typeimpactprojetid');
    }

    public function typeimpact()
    {
        return $this->belongsTo(TypeImpactModel::class, 'typeimpactid', 'id');
    }
    public function indicateur()
    {
        return $this->hasMany(TypeImpactprojetIndicateur::class, 'typeimpactprojetid', 'id');
    }

    public function dataindicateur()
    {
        return $this->belongsToMany(indicateur::class, 't_typeimpactprojet_indicateurs', 'typeimpactprojetid', 'indicateurid');
    }
}
