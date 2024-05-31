<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AffectationPermission extends Model
{
    use HasFactory, HasUuids, HasFactory, Notifiable;

    protected $table = "t__affectation_permission";

    protected $fillable = [
        'affectationid',
        'permissionid',
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permissionid', 'id')->WhereNotNull('name');
    }
}
