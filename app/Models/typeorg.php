<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class typeorg extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;
    protected $table="t_typeorganisation";
    protected $fillable = [
       'name',
   ];
}
