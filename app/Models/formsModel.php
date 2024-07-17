<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class formsModel extends Model
{
    use HasFactory,HasApiTokens, HasFactory, Notifiable,HasUuids;
    protected $table="forms";
    protected $fillable=[
        'title',
        'description',
        'otp_form'
    ];

    public function field(){
        return $this->belongsToMany(FieldsModel::class, 'fields', 'form_id', 'fieldtype_id')->withPivot(["fieldtype_id", "name"])->as('field');
    }

    public function fields(){
        return $this->hasMany(FieldsModel::class, 'form_id', 'id');
    }

    public function fieldsdata(){
        return $this->hasMany(UserHasForm::class, 'formid', 'id');
    }

}
