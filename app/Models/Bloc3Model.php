<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Bloc3Model extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;
    protected $table="t_gaps_bloc3";
    protected $fillable = [
        'id',
        'bloc2id',
        'cout_ambulatoire',
        'cout_hospitalisation',
        'cout_accouchement',
        'cout_cesarienne',
        'barriere',
        'pop_handicap',
        'couvertureDtc3',
        'mortaliteLessfiveyear',
        'covid19_nbrcas',
        'covid19_nbrdeces',
        'covid19_nbrtest',
        'covid19_nbrtest',
        'covid19_vacciDispo',
        'pourcentCleanWater',
        'malnutrition',
        'status',
        'deleted',
    ];


}
