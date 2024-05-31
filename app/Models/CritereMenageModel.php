<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CritereMenageModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;
    protected $table="t_menage_critere";
    protected $fillable = [
        'personne_id',
        'cretereid'
   ];
   public function datacritere()
   {
       return $this->belongsTo(CritereVulModel::class, 'cretereid','id');
   }
}
