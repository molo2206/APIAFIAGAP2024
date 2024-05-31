<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ConsultationCliniqueMobile extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    protected $table="t_consultation_clinique_mobil";

    protected $fillable = [
       "id",
       "activiteid",
       "homme_consulte_mob",
       "femme_consulte_mob",
       "consulte_moin_cinq_mob",
       "consulte_cinq_plus_mob"
    ];

}
