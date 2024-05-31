<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ConsultationExterneFosa extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    protected $table="t_consultation_externe_fosa";
    protected $fillable = [
       "id",
       "activiteid",
       "homme_consulte_fosa",
       "femme_consulte_fosa",
       "consulte_moin_cinq_fosa",
       "consulte_cinq_plus_fosa"
    ];
}
