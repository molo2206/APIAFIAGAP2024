<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Bloc2Model extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;
    protected $table="t_gaps_bloc2";
    protected $fillable = [
        'id',
        'bloc1id',
        'etat_infra',
        'equipement',
        'nbr_lit',
        'taux_occupation',
        'nbr_reco',
        'pop_eloigne',
        'pop_vulnerable',
        'status',
        'deleted'
    ];

    public function suite2(){
        return $this->belongsTo(Bloc3Model::class, 'id','bloc2id');
    }

}
