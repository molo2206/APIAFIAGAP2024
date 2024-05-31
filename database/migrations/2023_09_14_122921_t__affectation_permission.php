<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t__affectation_permission', function (Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('affectationid')->constrained('t__affectations')->nullable();
            $table->foreignUuid('permissionid')->constrained('t_permissions')->nullable();
            $table->timestamps();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
        });
    }
    public function down()
    {
        Schema::dropIfExists('t__affectation_permission');
    }
};
