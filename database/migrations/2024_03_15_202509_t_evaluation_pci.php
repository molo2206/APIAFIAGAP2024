<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_evaluation', function (Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('reponseid')->constrained('t_reponse')->nullable();
            $table->string('responsable')->nullable();
            $table->string('echeance')->nullable();
            $table->string('suivi')->nullable();
            $table->timestamps();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
        });
    }
    public function down()
    {
        Schema::dropIfExists('t_evaluation');
    }
};
