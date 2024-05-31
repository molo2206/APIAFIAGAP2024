<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_personnel_gap', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('personnelid')->constrained('t_personnels');
            $table->foreignUuid('gapid')->constrained('t_gaps_bloc1');
            $table->integer('nbr')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_personnel_gap');
    }
};
