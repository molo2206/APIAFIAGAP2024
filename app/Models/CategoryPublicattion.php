<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryPublicattion extends Model
{
    use HasFactory,HasUuids;
    protected $table = "category_publication";
    protected $fillable=[
        "name"
    ];
}
