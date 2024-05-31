<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Crise_Gap extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;
     protected $table="t__type_crise__gap";
     protected $fillable = [
        'gapid',
        "criseid"
    ];
    public function crise()
    {
        return $this->belongsTo(TypeCrise::class, 'criseid','id');
    }

}
