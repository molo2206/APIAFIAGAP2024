<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PersonnelGap extends Model
{
    use HasFactory,HasUuids,Notifiable;

    protected $table="t_personnel_gap";

    protected $fillable = [
              'personnelid',
              'gapid',
              'nbr',
    ];
    public function typepersonnel()
    {
        return $this->belongsTo(PersonnelModel::class, 'personnelid','id');
    }
}
