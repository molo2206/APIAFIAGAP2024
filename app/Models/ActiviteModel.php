<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ActiviteModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    protected $table="t_activites";

    protected $fillable = [
              "title_projet",
        "provinceid",
        "territoirid",
        "zoneid",
        "airid",
        "structureid",
        "org_make_repport",
        "org_make_oeuvre",
        "date_rapportage",
        "identifiant_project",
        "typeprojetid",
        "type_intervention",
        "axe_strategique",
        "odd",
        "description_activite",
        "statut_activite",
        "modalite",
        "src_financement",
        "vaccination",
        "malnutrition",
        "remarque",
        "date_debut_projet",
        "date_fin_projet",
        "cohp_relais",
        "type_reponse",
        'type_benef',
        "phone",
        "email",
        "cohp_relais"
   ];

    public function paquetappui()
    {
        return $this->hasMany(IndicateurActivite::class, 'activiteid', 'id');
    }
    public function databeneficecible()
    {
        return $this->belongsTo(BeneficeCible::class, 'id', 'activiteid');
    }

    public function databeneficeatteint()
    {
        return $this->belongsTo(BeneficeAtteint::class, 'id', 'activiteid');
    }

    public function dataconsultationexterne()
    {
        return $this->belongsTo(ConsultationExterneFosa::class, 'id', 'activiteid');
    }

    public function dataconsultationcliniquemobile()
    {
        return $this->belongsTo(ConsultationCliniqueMobile::class, 'id', 'activiteid');
    }
    public function data_organisation_make_rapport()
    {
        return $this->belongsTo(Organisation::class, 'org_make_repport', 'id');
    }
    public function data_organisation_mise_en_oeuvre()
    {
        return $this->belongsTo(Organisation::class, 'org_make_oeuvre', 'id');
    }
    public function dataprovince()
    {
        return $this->belongsTo(province::class, 'provinceid', 'id');
    }

    public function dataterritoir()
    {
        return $this->belongsTo(territoir::class, 'territoirid', 'id');
    }
    public function datazone()
    {
        return $this->belongsTo(zonesante::class, 'zoneid', 'id');
    }

    public function dataaire()
    {
        return $this->belongsTo(airesante::class, 'airid', 'id');
    }
    public function datastructure()
    {
        return $this->belongsTo(structureSanteModel::class, 'structureid', 'id');
    }

    public function indicataire()
    {
        return $this->belongsToMany(indicateur::class, 't_activite_indicateur', 'activiteid','indicateurid')->
        withPivot(['indicateurid'])->as('pci');
    }
}
