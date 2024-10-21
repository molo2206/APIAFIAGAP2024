<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('user_org_hasforms',function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('form_id')->constrained('form_has_project_has_org')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('t_users')->onDelete('cascade');
            $table->boolean('status')->default(false);
            $table->timestamps();
           });
    }

    public function down()
    {
        Schema::dropIfExists('user_org_hasforms');
    }
};
