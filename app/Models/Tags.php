<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    use HasFactory, HasUuids;
    protected $table = "t_tags";
    protected $fillable = [
        'name',
        'userid',
        'status'
    ];
}
