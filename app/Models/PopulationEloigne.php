<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PopulationEloigne extends Model
{
    use HasFactory,HasUuids,Notifiable;

    protected $table="t_population_eloigne_gap";

    protected $fillable = [
              'gapid',
              'localite',
              'nbr',
    ];
}
