<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_gaps_bloc3', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bloc2id')->constrained('t_gaps_bloc2');
            $table->integer('cout_ambulatoire');
            $table->integer('cout_hospitalisation');
            $table->integer('cout_accouchement');
            $table->integer('cout_cesarienne');
            $table->text('barriere');
            $table->integer('pop_handicap');
            $table->integer('couvertureDtc3');
            $table->integer('mortaliteLessfiveyear');
            $table->integer('covid19_nbrcas');
            $table->integer('covid19_nbrdeces');
            $table->integer('covid19_nbrtest');
            $table->integer('covid19_vacciDispo');
            $table->integer('pourcentCleanWater');
            $table->integer('malnutrition');
            $table->timestamps();
            $table->boolean('etat_top')->default(false);
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_gaps_bloc3');
    }
};
