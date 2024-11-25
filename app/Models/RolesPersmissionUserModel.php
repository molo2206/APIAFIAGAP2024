<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class RolesPersmissionUserModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

     protected $table="t_roles_has_permissions";
     protected $fillable = [
        'ressourceid',
        'affectation',
        'create',
        'update',
        'delete',
        'read',
        'status'
    ];

    public function affectation()
    {
        return $this->belongsTo(AffectationModel::class, 'affectation','id');
    }

    public function ressource()
    {
        return $this->belongsTo(RessourceModel::class ,'ressourceid','id');
    }
}
