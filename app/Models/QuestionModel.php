<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class QuestionModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;
    protected $table="t_question";
    protected $fillable = [
        'id',
        'enteteid',
        'name_question',
    ];

    public function datarubrique()
    {
        return $this->belongsTo(EnteteScoreModel::class, 'enteteid','id');
    }


}
