<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ReponseModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;
    protected $table="t_reponse";
    protected $fillable = [
        'id',
        'questionid',
        'gapid',
        'response'
    ];
    public function dataquestion()
    {
        return $this->belongsTo(QuestionModel::class, 'questionid','id');
    }

  
}
