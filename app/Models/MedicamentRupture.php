<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MedicamentRupture extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

    protected $table="t_medicament_rupture";

    protected $fillable = [
       'gapid',
       "medocid",
       "status",
       "deleted"
   ];

   public function medicament()
   {
        return $this->belongsTo(MedicamentModel::class, 'medocid','id');
   }


}
