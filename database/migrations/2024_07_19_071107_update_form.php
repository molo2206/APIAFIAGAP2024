<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->date('type')->after('description');
            $table->foreignUuid('project_id')->constrained('t_projets')->after('type');
        });
    }

    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->dropColumn('type');
        });
    }
};
