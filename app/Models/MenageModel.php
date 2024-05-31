<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MenageModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

     protected $table="t_menages";

     protected $fillable = [
        'code_menage', 
        "taille",
        'habitation',
        'origine',
        'userid',
        'site_id',
    ];

    public function sitedeplace(){
        return $this->belongsTo(SiteDeplaceModel::class,'site_id','id');
    }

    public function datapersonne()
    {
        return $this->hasMany(PersonnesModel::class, 'manageid','id')->orderBy('created_at', 'asc');
    }

}
