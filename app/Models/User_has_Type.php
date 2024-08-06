<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_has_Type extends Model
{
    use HasFactory, HasUuids;
    protected $table = "user_has_type";
    protected $fillable = [
        'userid',
        'typeid',
    ];

}
