<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class DetailProjetVaccines extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;
    protected $table="t_detail_projet_vaccines";
    protected $fillable = [
        'id',
        'activiteid',
        'typevaccinid',
        'nbr_vaccine',
    ];

    public function Vaccination(){
        return $this->belongsTo(TypeVaccin::class, 'typevaccinid','id');
    }
}
