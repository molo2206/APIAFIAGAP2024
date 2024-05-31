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
        Schema::create('t_organisation', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('description')->nullable();
            $table->string('sigle')->nullable();
            $table->text('logo')->default('avatar.jpg');
            $table->string('adresse')->default(true);
            $table->text('activite')->nullable();
            $table->foreignUuid('typeorgid')->constrained('t_typeorganisation');
            $table->string('pointfocal');
            $table->integer('status');
            $table->integer('delete');
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
        Schema::dropIfExists('t_organisation');
    }
};
