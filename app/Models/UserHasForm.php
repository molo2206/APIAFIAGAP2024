<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasForm extends Model
{
    use HasFactory, HasUuids;
    protected $table ="user_has_forms";
    protected $fillable =[
        'userid',
        'formid',
        'structure_id'
    ];

    public function response(){
        return $this->belongsToMany(FieldsModel::class, 'response_forms', 'hasformid', 'field_id')->withPivot(["hasformid", "field_id","value"]);
    }
    public function form(){
        return $this->belongsTo(formsModel::class,'formid', 'id');
    }

    public function structure(){
        return $this->belongsTo(structureSanteModel::class,'structure_id', 'id');
    }

}
