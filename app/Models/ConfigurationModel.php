<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ConfigurationModel extends Model
{
    use HasFactory, HasUuids, Notifiable;

    protected $table = 't_configuration';

    protected $fillable = [
        'organisation_name',
        'email',
        'phone',
        'politique',
        'condition',
        'apropos',
        'logo',
        'fiveicone',
        'version',
        'app_store',
        'google_play',
        'description',
        'adresse',
        'id'
    ];
}
