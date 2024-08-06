<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type_users extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'type_user';
    protected $fillable = [
        "id",
        "name",
    ];
}
