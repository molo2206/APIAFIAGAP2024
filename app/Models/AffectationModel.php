<?php

namespace App\Models;

use Google\Service\AndroidEnterprise\Resource\Permissions;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AffectationModel extends Model
{
    use HasFactory, HasUuids, HasFactory, Notifiable;

    protected $table = "t__affectations";

    protected $fillable = [
        'orgid',
        'roleid',
        'userid',
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userid', 'id');
    }


    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'roleid', 'id');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'orgid', 'id');
    }

    public function allpermission()
    {
        return $this->hasMany(AffectationPermission::class, 'affectationid', 'id');
    }

    public function affectationpermission()
    {
        return $this->belongsToMany(AffectationPermission::class, 't__affectation_permission', 'permissionid', 'affectationid')
            ->withPivot(["affectationid"]);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 't__affectation_permission', 'affectationid', 'permissionid')->as('access');
    }
}
