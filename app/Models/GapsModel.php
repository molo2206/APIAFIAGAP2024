<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class GapsModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;
    protected $table = "t_gaps_bloc1";
    protected $fillable = [
        'id',
        'title',
        'provinceid',
        'territoirid',
        'zoneid',
        'airid',
        'orgid',
        'population',
        'pop_deplace',
        'pop_retourne',
        'pop_site',
        'criseid',
        'userid',
        'orguserid',
        'children',
        'semaine_epid',
        'annee_epid',
        'etat_top',
        'status',
        'deleted',
        'dateadd'
    ];

    public function datauser()
    {
        return $this->belongsTo(User::class, 'userid', 'id');
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
    public function datamaladie()
    {
        return $this->hasMany(MaladiedGap::class, 'gapid', 'id');
    }

    public function datamedicament()
    {
        return $this->hasMany(MedicamentRupture::class, 'gapid', 'id');
    }

    public function datapartenaire()
    {
        return $this->hasMany(PartenairePresntModel::class, 'gapid', 'id');
    }

    public function datatypepersonnel()
    {
        return $this->hasMany(PersonnelGap::class, 'gapid', 'id');
    }
    public function datapopulationEloigne()
    {
        return $this->hasMany(PopulationEloigne::class, 'gapid', 'id');
    }
    public function datastructure()
    {
        return $this->belongsTo(structureSanteModel::class, 'orgid', 'id');
    }

    public function datascorecard()
    {
        return $this->hasMany(ReponseModel::class, 'gapid', 'id');
    }
    public function suite1()
    {
        return $this->belongsTo(Bloc2Model::class, 'id', 'bloc1id');
    }

    public function allcrise()
    {
        return $this->hasMany(Crise_Gap::class, 'gapid', 'id');
    }

    public function images()
    {
        return $this->hasMany(ImageGapModel::class, 'gapid', 'id');
    }

    public function maladiegap()
    {
        return $this->belongsToMany(Maladie::class, 't_maladie_gap', 'gapid', 'maladieid')->withPivot(["nbrCas", "nbrDeces",])->as('maladie');
    }

    public function medicamentrupture()
    {
        return $this->belongsToMany(MedicamentModel::class, 't_medicament_rupture', 'gapid', 'medocid')->withPivot(["status"])->as('medicament_rupture');
    }

    public function partenairegap()
    {
        return $this->belongsToMany(Organisation::class, 't_partenaire_presents', 'gapid', 'orgid')
            ->withPivot(["contact_point_facal", "orgid", "date_debut", "date_fin"])->as('partenaire_presents');
    }

    public function indicateurgap()
    {
        return $this->belongsToMany(indicateur::class, 't_org_indicateur', 'gapid', 'orgid')->withPivot(["indicateurid", "orgid"])->as('partenaire');
    }

    public function typepersonnelgap()
    {
        return $this->belongsToMany(PersonnelModel::class, 't_personnel_gap', 'gapid', 'personnelid')->withPivot(["personnelid", "nbr"])->as('typepersonnel');
    }

    public function crisegap()
    {
        return $this->belongsToMany(TypeCrise::class, 't__type_crise__gap', 'gapid', 'criseid')->withPivot(["criseid"])->as('crise');
    }

    public function populationeloignegap()
    {
        return $this->belongsToMany(PopulationEloigne::class, 't_population_eloigne_gap', 'gapid', 'localite')->withPivot(["localite", "nbr"])->as('population_eloigne');
    }

    public function imagesgap()
    {
        return $this->belongsToMany(ImageGapModel::class, 't_image_gap', 'gapid', 'image')->withPivot(["image"])->as('images_gap');
    }

    public function scorecardgap()
    {
        return $this->belongsToMany(QuestionModel::class, 't_reponse', 'gapid','questionid')->
        withPivot(['questionid'])->as('scorecardgap');
    }

    public function gap_appuis()
    {
        return $this->hasMany(GapAppuiModel::class,'gapid', 'id');
    }

    public function gap_appuiss()
    {
        return $this->hasMany(GapAppuiModel::class,'gapid', 'id')->where('children','!=' , null);
    }

    public function gap_appui()
    {
        return $this->belongsToMany(GapAppuiModel::class, 't_gaps_appui', 'gapid','key')->
        withPivot(['gapid'])->as('gap_appui');
    }

    public  function QuestionReponse()
    {
        return $this->belongsToMany(ReponseModel::class, 't_reponse', 'questionid', 'gapid')
        ->withPivot(["gapid"]);
    }

}
