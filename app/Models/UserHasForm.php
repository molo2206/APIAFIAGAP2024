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
    ];

    public function response(){
        return $this->belongsToMany(FieldsModel::class, 'response_forms', 'hasformid', 'field_id')->withPivot(["hasformid", "field_id","value"]);
    }
}
