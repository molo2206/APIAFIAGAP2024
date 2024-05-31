<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class BeneficeAtteint extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    protected $table="t_benef_atteint";
    protected $fillable = [
        "id",
        "activiteid",
        "homme_atteint",
        "femme_atteint",
        "enfant_garcon_moin_cinq",
        "enfant_fille_moin_cinq",
        "homme_atteint_handicap",
        "personne_atteint_handicap",
        "total_atteint",
   ];

}
