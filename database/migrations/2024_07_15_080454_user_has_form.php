<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('user_has_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('userid')->constrained('t_users')->onDelete('cascade');
            $table->foreignUuid('formid')->constrained('forms')->onDelete('cascade');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('user_has_forms');
    }
};
