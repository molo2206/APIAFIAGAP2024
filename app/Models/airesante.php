<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class airesante extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     protected $table="t_aire_sante";
     protected $fillable = [
        'name',
        "zoneid",
        "nbr_population"
    ];

    public function zonesante(){
        return $this->belongsTo(zonesante::class, 'zoneid','id');
     }
}
