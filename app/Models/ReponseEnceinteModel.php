<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ReponseEnceinteModel extends Model
{
    use HasFactory,HasUuids,Notifiable;

    protected $table="t_reponse_enceinte";
    protected $fillable = [
              'questionid',
              'personneid',
              'reponse'
    ];
}
