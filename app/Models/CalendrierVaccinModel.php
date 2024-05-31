<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CalendrierVaccinModel extends Model
{
    use HasFactory, HasUuids, Notifiable;

    protected $table = "t_calendrier_vaccin";
    protected $fillable = [
        'name',
        'personneid',
    ];
}
