<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ConsultationExterneFosaProjet extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $table = "t_consultation_externe_fosa_projet";
    protected $fillable = [
        "id",
        "activiteid",
        'orguserid',
        "consulte_moin_cinq_fosa",
        "consulte_cinq_dix_sept_fosa",
        "homme_fosa_dix_huit_plus_fosa",
        "femme_fosa_dix_huit_plus_fosa",
        
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
