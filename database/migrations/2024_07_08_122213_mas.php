<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mas', function (Blueprint $table)
        {
            $table->uuid('id')->primary();

            $table->foreignUuid('activiteid')->constrained('t_activite_projets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('cas_mas')->constrained('type_mas');
            $table->foreignUuid('orguserid')->constrained('t_organisation');

            $table->integer('garcon')->nullable();
            $table->integer('fille')->nullable();
            $table->integer('total')->nullable();

            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('mas');
    }
};
