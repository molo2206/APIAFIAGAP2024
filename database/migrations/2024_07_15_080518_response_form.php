<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('response_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('hasformid')->constrained('user_has_forms')->onDelete('cascade');
            $table->foreignUuid('field_id')->constrained('fields')->onDelete('cascade');
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('response_forms');
    }
};
