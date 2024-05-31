<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class territoir extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = "t_territoire";
    protected $fillable = [
        'name',
        "provinceid",
        "id",
    ];
    public function zonesante()
    {
        return $this->hasMany(zonesante::class, 'territoirid', 'id');
    }

    public function province(){
       return $this->belongsTo(province::class, 'provinceid','id');
    }
}
