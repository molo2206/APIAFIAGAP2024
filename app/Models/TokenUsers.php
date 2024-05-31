<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenUsers extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'tokensuser';
    protected $fillable = [
        'id',
        'userid',
        'token'
    ];
}
