<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EnteteScoreModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;
    protected $table="t_entete_score_card";
    protected $fillable = [
        'id',
        'name_entete',
    ];
       public function dataquestion()
    {
        return $this->hasMany(QuestionModel::class, 'enteteid','id');
    }
}
