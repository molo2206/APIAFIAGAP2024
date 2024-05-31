<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AlertModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

     protected $table="t_alert";
     protected $fillable = [
       'name_point_focal',
        "phone",
        "communaute",
        "airid",
        "date_notification",
        "datealert",
        "timealert",
        "nbr_touche",
        "dece_disponible",
        "nbr_dece",
        "animal_malade",
        "animal_mort",
        "evenement",
        "mesure",
        "maladieid",
        "nb_animal_malade",
        "nb_animal_mort",
        "date_detection",
        "time_detection",
        "userid",
        "userid_valider",
        "children",
        "orguserid",
        "description",
        "status"
    ];
  public function dataaire()
    {
        return $this->belongsTo(airesante::class, 'airid','id');
    }
      public function maladie()
    {
        return $this->belongsTo(Maladie::class, 'maladieid','id');
    }
    
     public function datauser(){
        return $this->belongsTo(User::class, 'userid','id');
    }
     public function images(){
        return $this->hasMany(ImageAlertModel::class, 'alertid','id');
    }
     public function imagesalert()
    {
        return $this->belongsToMany(ImageAlertModel::class, 't_image_alert', 'alertid', 'image')
        ->withPivot(["alertid"])->as('images_alert');
    }
}
