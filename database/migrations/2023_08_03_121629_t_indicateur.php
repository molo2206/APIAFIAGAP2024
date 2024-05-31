<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_indicateur', function (Blueprint $table){
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->foreignUuid('orgid')->constrained('t_organisation')->nullable();
            $table->integer('status');
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
        Schema::dropIfExists('t_indicateur');
    }
};
