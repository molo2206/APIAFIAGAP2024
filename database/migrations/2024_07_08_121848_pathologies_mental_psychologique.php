<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pathologies_mental_psychologique', function (Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('activiteid')->constrained('t_activite_projets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('orguserid')->constrained('t_organisation');
            $table->string('statut')->nullable();
            $table->integer('garcon_moin_cinq')->nullable();
            $table->integer('garcon_cinq_dix_septe')->nullable();
            $table->integer('fille_moin_cinq')->nullable();
            $table->integer('fille_cinq_dix_septe')->nullable();
            $table->integer('homme_dix_huite_cinquante_neuf')->nullable();
            $table->integer('homme_cinquante_neuf')->nullable();
            $table->integer('femme_dix_huite_cinquante_neuf')->nullable();
            $table->integer('femme_cinquante_neuf')->nullable();
            $table->integer('total')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pathologies_mental_psychologique');
    }
};
