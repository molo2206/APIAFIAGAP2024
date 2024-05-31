<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_action_correctif_pci', function (Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('evaluationid')->constrained('t_evaluation')->nullable();
            $table->string('action')->nullable();
            $table->timestamps();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
        });
    }
    public function down()
    {
        Schema::dropIfExists('t_action_correctif_pci');
    }
};
