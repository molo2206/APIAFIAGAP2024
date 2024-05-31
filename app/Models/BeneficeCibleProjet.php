<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class BeneficeCibleProjet extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $table = "t_benef_cible_projet";
    protected $fillable = [
        "id",
        "activiteid",
        "typeimpactid",
        'orguserid',
        "homme_cible",
        "femme_cible",
        "enfant_garcon_moin_cinq",
        "enfant_fille_moin_cinq",
        "homme_cible_handicap",
        "femme_cible_handicap",
        "personne_cible_handicap",

        "garcon_cible_cinq_dix_septe",
        "fille_cible_cinq_dix_septe",

        "homme_cible_dix_huit_cinquante_neuf",
        "femme_cible_dix_huit_cinquante_neuf",

        "homme_cible_plus_cinquante_neuf",
        "femme_cible_plus_cinquante_neuf",

        "total_cible"
    ];

    public function indicateur()
    {
        return $this->belongsTo(indicateur::class, 'indicateurid', 'id');
    }

    public function structuresante()
    {
        return $this->belongsTo(structureSanteModel::class, 'structureid', 'id');
    }

    public function projet(){
        return $this->belongsTo(ProjetModel::class, 'projetid', 'id');
    }
    public function typeimpact(){
        return $this->belongsTo(TypeImpactModel::class, 'typeimpactid', 'id');
    }

}
