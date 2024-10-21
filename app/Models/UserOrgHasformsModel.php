<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOrgHasformsModel extends Model
{
    use HasFactory, HasUuids;
    protected $table = "user_org_hasforms";
    protected $fillable = [
        'form_id',
        'user_id',
    ];

    public function user_org_hasforms()
    {
        return $this->belongsTo(Form_has_project_has_orga::class, 'form_id', 'id');
    }

    public function user(){
         return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
