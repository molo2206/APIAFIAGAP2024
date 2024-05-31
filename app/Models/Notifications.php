<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory, HasUuids;
    protected $table = "t_notifications";
    protected $fillable = [
        "user_id",
        "title",
        "description",
        "type",
        "id_type",
    ];
}
