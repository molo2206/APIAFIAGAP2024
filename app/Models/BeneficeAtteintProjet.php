<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class BeneficeAtteintProjet extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    protected $table="t_benef_atteint_projet";
    protected $fillable = [
        "id",
        "activiteid",
        'orguserid',
        "homme_atteint",
        "femme_atteint",
        "enfant_garcon_moin_cinq",
        "enfant_fille_moin_cinq",
        "homme_atteint_handicap",
        "personne_atteint_handicap",

        "garcon_atteint_cinq_dix_septe",
        "fille_atteint_cinq_dix_septe",

        "homme_atteint_dix_huit_cinquante_neuf",
        "femme_atteint_dix_huit_cinquante_neuf",

        "homme_atteint_plus_cinquante_neuf",
        "femme_atteint_plus_cinquante_neuf",

        "total_atteint",
   ];

   public function indicateur(){
         return $this->belongsTo(indicateur::class,'indicateurid','id');
   }

   public function structuresante(){
       return $this->belongsTo(structureSanteModel::class,'structureid','id');
   }


}
