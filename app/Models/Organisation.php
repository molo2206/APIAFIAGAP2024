<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Organisation extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     protected $table="t_organisation";

     protected $fillable = [
        'name',
        'email',
        'phone',
        'description',
        'logo',
        'sigle',
        'adresse',
        'pointfocal',
        'typeorgid',
        'status',
        'delete'
    ];

    public function allindicateur()
    {
        return $this->hasMany(org_indicateur::class, 'orgid','id');
    }

    public function type_org(){
        return $this->belongsTo(typeorg::class, 'typeorgid','id');
    }

//================================================================================================

public function databeneficecible()
{
    return $this->hasMany(BeneficeCibleProjet::class, 'orguserid', 'id');
}

public function databeneficeatteint()
{
    return $this->hasMany(BeneficeAtteintProjet::class, 'orguserid', 'id');
}

public function dataconsultationexterne()
{
    return $this->hasMany(ConsultationExterneFosaProjet::class, 'orguserid', 'id');
}

public function dataconsultationcliniquemobile()
{
    return $this->hasMany(ConsultationCliniqueMobileProjet::class, 'orguserid', 'id');
}

public function data_organisation_make_rapport()
{
    return $this->belongsTo(Organisation::class, 'org_make_repport', 'id');
}

public function data_organisation_mise_en_oeuvre()
{
    return $this->belongsTo(Organisation::class, 'org_make_oeuvre', 'id');
}

public function struturesantes()
{
    return $this->belongsToMany(structureSanteModel::class, 't_rayon_action_projet', 'projetid', 'structureid');
}

public function autresinfoprojet()
{
    return $this->belongsTo(AutreInfoProjets::class, 'id', 'orguserid');
}

public function typeimpact()
{
    return $this->belongsToMany(TypeImpactModel::class, 't_reponse_indicateur_projet', 'projetid', 'typeimpactid');
}

public function datatypeimpact()
{
    return $this->hasMany(IndicateurProjetModel::class, 'projetid', 'id');
}

public function typeprojet()
{
    return $this->belongsTo(TypeProjet::class, 'typeprojetid', 'id');
}

}
