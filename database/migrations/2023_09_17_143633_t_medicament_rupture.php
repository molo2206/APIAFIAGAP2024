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
        Schema::create('t_medicament_rupture', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('medocid')->constrained('t_medicament')->nullable();
            $table->foreignUuid('gapid')->constrained('t_gaps_bloc1')->nullable();
            $table->timestamps();
            $table->boolean('etat_top')->default(false);
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::dropIfExists('t_medicament_rupture');
    }
};
