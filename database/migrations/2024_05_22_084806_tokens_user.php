<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('tokensuser', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('userid')->constrained('t_users')->nullable();
            $table->text('token')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tokensuser');
    }
};
