<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t__affectations', function (Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('orgid')->constrained('t_organisation')->nullable();
            $table->foreignUuid('roleid')->constrained('t_roles')->nullable();
            $table->foreignUuid('userid')->constrained('t_users')->nullable();
            $table->timestamps();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('t__affectations');
    }
};
