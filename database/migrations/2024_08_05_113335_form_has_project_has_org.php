<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
       Schema::create('form_has_project_has_org',function(Blueprint $table){
        $table->uuid('id')->primary();
        $table->foreignUuid('form_id')->constrained('forms')->onDelete('cascade');
        $table->foreignUuid('project_id')->constrained('t_projets')->onDelete('cascade');
        $table->foreignUuid('org_id')->constrained('t_organisation')->onDelete('cascade');
        $table->boolean('status')->default(false);
        $table->boolean('deleted')->default(false);
        $table->timestamps();
       });
    }


    public function down()
    {

    }
};
