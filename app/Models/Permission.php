<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Permission extends Model
{
    use HasFactory,HasUuids,HasFactory,Notifiable;

    protected $table="t_permissions";

    protected $fillable = [
              'name',
              'psedo',
    ];

}
