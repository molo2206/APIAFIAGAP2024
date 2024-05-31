<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class codeValidation extends Model
{
    use HasFactory;
    protected $table = "t_code_validations";
    protected $fillable = ['code', 'status', 'email'];

}
