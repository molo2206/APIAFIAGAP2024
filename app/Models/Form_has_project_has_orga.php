<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form_has_project_has_orga extends Model
{
    use HasFactory, HasUuids;
    protected $table = "form_has_project_has_org";
    protected $fillable = ['form_id', 'project_id', 'org_id','otp_form'];

    public function form()
    {
        return $this->belongsTo(formsModel::class, 'form_id', 'id');
    }
    public function organisation(){
        return $this->belongsTo(Organisation::class, 'org_id', 'id');
    }
    public function project(){
        return $this->belongsTo(ProjetModel::class, 'project_id', 'id');
    }

    public function hasform(){
        return $this->hasMany(UserHasForm::class, 'formid', 'id');
    }
}
