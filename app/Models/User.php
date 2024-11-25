<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = "t_users";

    protected $fillable = [
        'full_name',
        'email',
        'pswd',
        'phone',
        'gender',
        'status',
        'deleted',
        'profil',
        'dateBorn',
        'id',
        'fingerprint'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'pswd',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function affectation()
    {
        return $this->hasMany(AffectationModel::class, 'userid', 'id');
    }
    public function engagement_as_permission()
    {
        return $this->belongsTo(AffectationModel::class, 'id', 'userid');
    }

    public function affectation1()
    {
        return $this->belongsTo(Organisation::class, 't__affectations', 'userid', 'orgid');
    }

    public function token()
    {
        return $this->hasMany(TokenUsers::class, 'userid', 'id');
    }

    public function tags()
    {
        return $this->hasMany(Tags::class, 'userid', 'id');
    }

    public function checkPermission($name)
    {
        $exis = $this->engagement_as_permission->permission()->where('name', $name)
            ->where('status', 1)->where('deleted', 0)->first();
        if ($exis) {
            if ($exis->access) {
                return true;
            } else {
                return false;
            }
        }
        return $exis;
    }
    public function checkPermissions($name, $action)
    {
        $exis = $this->engagement_as_permission->permission()->where('name', $name)->first();
        if ($exis) {
            if ($exis->access->$action) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function typeUser()
    {
        return $this->belongsToMany(Type_users::class, 'user_has_type', 'userid', 'typeid');
    }

    public function forms()
    {
        return $this->hasMany(UserOrgHasformsModel::class, 'user_id', 'id');
    }
}
