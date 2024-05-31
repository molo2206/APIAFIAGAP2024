<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ProjetModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;


    protected $table = "t_projets";
    protected $fillable = [
        'title_projet',
        'provinceid',
        'territoir',
        'zoneid',
        'airid',
        'structureid',
        'userid',
        'orguserid',
        'org_make_repport',
        'org_make_oeuvre',
        'identifiant_project',
        'typeprojetid',
        'type_intervention',
        'axe_strategique',
        'odd',
        'description_activite',
        'statut_activite',
        'modalite',
        'src_financement',
        'bailleur_de_fond',
        'fond_louer_projet',
        'fond_operationel_disponible',
        'cohp_relais',
        'date_debut_projet',
        'date_fin_projet',
        'type_benef',
        'id',
        'typeid',
        "presence_physique",
        "institu_app",
        "fourniture_info",
        "participation_com",
        "mecanisme_evaluation",
        "collect_gestion",
        "approche_app",
        "resultat_collectif",
        "axe_strategique",
        "projet_vise",
    ];

    public function databeneficecible()
    {
        return $this->hasMany(BeneficeCibleProjet::class, 'projetid', 'id');
    }

    public function databeneficeatteint()
    {
        return $this->hasMany(BeneficeAtteintProjet::class, 'projetid', 'id');
    }

    public function dataconsultationexterne()
    {
        return $this->hasMany(ConsultationExterneFosaProjet::class, 'projetid', 'id');
    }

    public function dataconsultationcliniquemobile()
    {
        return $this->hasMany(ConsultationCliniqueMobileProjet::class, 'projetid', 'id');
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
        return $this->belongsTo(AutreInfoProjets::class, 'id', 'projetid');
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

    public function cohp_relais()
    {
        return $this->belongsTo(CohpModel::class, 'cohp_relais', 'id');
    }
}
