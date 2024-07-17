<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cas_referes', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->foreignUuid('activiteid')->constrained('t_activite_projets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('orguserid')->constrained('t_organisation');
            $table->foreignUuid('structureid')->constrained('t_structure_sanitaire')->nullable();
            $table->integer('nbr_refere')->nullable();
            $table->text('motif')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cas_referes');
    }
};
