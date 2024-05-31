<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class RoleMenageModel extends Model
{
    use HasFactory,HasUuids,Notifiable;

    protected $table="t_roles_menage";
    protected $fillable = [
              'name',
    ];
}
