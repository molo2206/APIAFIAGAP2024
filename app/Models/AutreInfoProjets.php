<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AutreInfoProjets extends Model
{
  use HasApiTokens, HasFactory, Notifiable, HasUuids;
  protected $table = "t_autres_projet";

  protected $fillable = [
    'id',
    'activiteid',
    'indicateurid',
    'structureid',
    'description_activite',
    'statut_activite',
    'nbr_vaccin',
    'type_vaccin',
    'nbr_cpn',
    'nbr_malnutrition',
    'nbr_accouchement',
    'email',
    'phone',
    'remarque',
    'date_rapportage',
    'orguserid',
  ];

  public function projet(){

  }

  public function indicateur()
  {
    return $this->belongsTo(indicateur::class, 'indicateurid', 'id');
  }

  public function structuresante()
  {
    return $this->belongsTo(structureSanteModel::class, 'structureid', 'id');
  }

  public function infosVaccination()
  {
    return $this->belongsToMany(TypeVaccin::class, 't_detail_projet_vaccines', 'autreid', 'typevaccinid');
  }

  public function infosVaccinations(){
    return $this->hasMany(DetailProjetVaccines::class, 'activiteid','id');
  }

  public function cohp_relais(){
    return $this->belongsTo(CohpModel::class, 'cohp_relais', 'id');
  }
}
