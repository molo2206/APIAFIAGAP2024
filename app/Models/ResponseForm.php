<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseForm extends Model
{
    use HasFactory,HasUuids;
    protected $table="response_forms";
    protected $fillable = [
        'hasformid',
        'field_id',
        'value',
    ];
}
