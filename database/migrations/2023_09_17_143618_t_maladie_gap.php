<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_maladie_gap', function (Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('gapid')->constrained('t_gaps_bloc1')->nullable();
            $table->foreignUuid('maladieid')->constrained('t_maladie')->nullable();
            $table->integer('nbrCas')->nullable();
            $table->integer('nbrDeces')->nullable();
            $table->timestamps();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
        });
    }
    public function down()
    {
        Schema::dropIfExists('t_maladie_gap');
    }
};
