<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('t_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('t_users')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('t_notifications');
    }
};
