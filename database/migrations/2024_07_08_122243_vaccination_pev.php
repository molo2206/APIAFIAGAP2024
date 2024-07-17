<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vaccination_pev', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->foreignUuid('activiteid')->constrained('t_activite_projets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('orguserid')->constrained('t_organisation');
            $table->foreignUuid('typevacc_id')->constrained('type_vaccinations')->nullable();
            $table->integer('nbr_vaccinations')->nullable();
            $table->integer('nbr_rattrap')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vaccination_pev');
    }
};
