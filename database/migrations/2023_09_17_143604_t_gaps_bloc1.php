<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_gaps_bloc1', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->foreignUuid('provinceid')->constrained('t_province');
            $table->foreignUuid('zoneid')->constrained('t_zone');
            $table->foreignUuid('airid')->constrained('t_aire_sante');
            $table->foreignUuid('orgid')->constrained('t_organisation');
            $table->integer('population');
            $table->integer('pop_deplace');
            $table->integer('pop_retourne');
            $table->integer('pop_site');
            $table->foreignUuid('criseid')->constrained('t_type_crise');
            $table->string('semaine_epid');
            $table->string('annee_epid');
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
        Schema::dropIfExists('t_gaps_bloc1');
    }
};
