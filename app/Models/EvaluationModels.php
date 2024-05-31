<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EvaluationModels extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;
    protected $table = "t_evaluation";
    protected $fillable = [
        'scoreid',
        'thematiqueid',
        'questionid',
        'correction',
        "responsable",
        "echeance",
        "suivi",
    ];
    public function datarubrique()
    {
        return $this->belongsTo(EnteteScoreModel::class, 'thematiqueid', 'id');
    }
    public function dataquestion()
    {
        return $this->belongsTo(QuestionModel::class, 'questionid', 'id');
    }
}
