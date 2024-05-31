<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_partenaire_presents', function (Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('gapid')->constrained('t_gaps_bloc1')->nullable();
            $table->foreignUuid('orgid')->constrained('t_organisation')->nullable();
            $table->string('paquetAppui')->nullable();
            $table->string('contact_point_facal')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->timestamps();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
        });
    }
    public function down()
    {
        Schema::dropIfExists('t_partenaire_presents');
    }
};
