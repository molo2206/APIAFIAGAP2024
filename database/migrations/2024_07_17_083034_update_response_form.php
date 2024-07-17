<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('response_forms', function (Blueprint $table) {
            $table->date('date_rapportage')->after('value');
            $table->foreignUuid('structureid')->constrained('t_structure_sanitaire')->after('date_rapportage');
        });
    }

    public function down()
    {
        Schema::table('response_forms', function (Blueprint $table) {
            $table->dropColumn('date_rapportage');
            $table->dropColumn('structureid');
        });
    }
};
