<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_gaps_bloc2', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bloc1id')->constrained('t_gaps_bloc1');
            $table->integer('etat_infra');
            $table->text('equipement');
            $table->integer('nbr_lit');
            $table->integer('taux_occupation');
            $table->integer('nbr_reco');
            $table->integer('pop_eloigne');
            $table->integer('pop_vulnerable');
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
        Schema::dropIfExists('t_gaps_bloc2');
    }
};
