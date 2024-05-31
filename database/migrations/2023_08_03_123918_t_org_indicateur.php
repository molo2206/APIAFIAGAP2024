<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_org_indicateur', function (Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('orgid')->constrained('t_organisation')->nullable();
            $table->foreignUuid('indicateurid')->constrained('t_indicateur')->nullable();
            $table->integer('status');
            $table->integer('deleted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_org_indicateur');
    }
};
