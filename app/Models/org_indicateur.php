<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class org_indicateur extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     protected $table="t_org_indicateur";
     protected $fillable = [
        "orgid",
        "indicateurid",
        "gapid",
        "status",
        "deleted"
    ];

    public function paquetappui()
    {
        return $this->belongsTo(indicateur::class, 'indicateurid','id');
    }

}
