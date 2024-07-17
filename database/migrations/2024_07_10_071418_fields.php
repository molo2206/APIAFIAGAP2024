<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('form_id')->constrained('forms')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('fieldtype_id')->constrained('fieldtypes');
            $table->string('name')->nullable();
            $table->boolean('isOptional')->default(false);
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
       Schema::dropIfExists('fields');
    }
};
