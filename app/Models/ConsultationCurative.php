<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ConsultationCurative extends Model
{
    use HasFactory, HasUuids, Notifiable;

    protected $table = 't_configuration';

    protected $fillable = [
        'activiteid',
        'orguserid',
        'statut',
        'garcon_moin_cinq',
        'garcon_cinq_dix_septe',
        'fille_moin_cinq',
        'fille_cinq_dix_septe',
        'homme_dix_huite_cinquante_neuf',
        'homme_cinquante_neuf',
        'femme_dix_huite_cinquante_neuf',
        'femme_cinquante_neuf',
        'total',
        'id'
    ];
}
