<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ConsultationCliniqueMobileProjet extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    protected $table="t_consultation_clinique_mobil_projet";
    protected $fillable = [
       "id",
       "activiteid",
       "structureid",
       "indicateurid",

       "consulte_moin_cinq_mob",
       "consulte_cinq_dix_sept_mob",
       "homme_dix_huit_plus_mob",
       "femme_dix_huit_plus_mob",
       'orguserid'
       
    ];
    public function indicateur()
    {
        return $this->belongsTo(indicateur::class, 'indicateurid', 'id');
    }

    public function structuresante()
    {
        return $this->belongsTo(structureSanteModel::class, 'structureid', 'id');
    }
}
