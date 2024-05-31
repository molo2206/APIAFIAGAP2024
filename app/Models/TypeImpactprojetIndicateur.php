<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TypeImpactprojetIndicateur extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;
    protected $table="t_typeimpactprojet_indicateurs";
    protected $filable=[
          'typeimpactprojetid',
          'indicateurid'
    ];

    public function indicateur(){
        return $this->belongsTo(indicateur::class, 'indicateurid','id');
    }
}
