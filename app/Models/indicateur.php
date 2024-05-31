<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class indicateur extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     protected $table="t_indicateur";

     protected $fillable = [
        'name',
        'value',
        "status",
        "deleted",
        "type_reponseid"
    ];

    public function pronviceville()
    {
        return $this->belongsToMany(ville::class, 't_province', 'provinceid');
    }
}
