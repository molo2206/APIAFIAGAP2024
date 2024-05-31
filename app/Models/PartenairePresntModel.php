<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PartenairePresntModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    protected $table="t_partenaire_presents";

    protected $fillable = [
       'gapid',
       "orgid",
       "contact_point_facal",
       "date_debut",
       "date_fin",
       "status",
       "deleted"
   ];

   public function partenaire()
   {
        return $this->belongsTo(Organisation::class, 'orgid','id');
   }

}
