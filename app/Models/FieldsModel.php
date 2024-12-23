<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldsModel extends Model
{
    use HasFactory;
    protected $table = 'fields';
    protected $fillable = [
        'name',
        'label',
        'form_id',
        'fieldtype_id',
        'isOptional',
        'number'
    ];

    public function typefield(){
        return $this->belongsTo(FieldsTypeModel::class, 'fieldtype_id', 'id');
    }

}
