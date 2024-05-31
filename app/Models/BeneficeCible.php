<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class BeneficeCible extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    protected $table="t_benef_cible";
    protected $fillable = [
       "id",
       "activiteid",
       "homme_cible",
       "femme_cible",
       "enfant_garcon_moin_cinq",
       "enfant_fille_moin_cinq",
       "homme_cible_handicap",
       "femme_cible_handicap",
       "personne_cible_handicap",
       "total_cible"
   ];


}
