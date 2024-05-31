<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PositiontionPartenaireModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;
    protected $table = "t_positionnement_partenaire";
    protected $fillable = [
        'gap_apuis_id', 'date_approxitive_debut_appui', 'status', 'budget_disponible', 'message', 'userid', 'type_paiement', 'devise', 'qte'
    ];

    public function gap_appuis()
    {
        return $this->belongsTo(GapAppuiModel::class, 'gap_apuis_id', 'id');
    }
}
