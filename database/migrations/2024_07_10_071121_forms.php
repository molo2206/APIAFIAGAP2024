<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->nullable();
            $table->string('otp_form')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('forms');
    }

};
