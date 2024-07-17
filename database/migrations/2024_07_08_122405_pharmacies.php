<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pharmacies', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->foreignUuid('activiteid')->constrained('t_activite_projets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('orguserid')->constrained('t_organisation');
            $table->foreignUuid('medicamentid')->constrained('t_medicament');
            $table->integer('nbr_rupture')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pharmacies');
    }
};
