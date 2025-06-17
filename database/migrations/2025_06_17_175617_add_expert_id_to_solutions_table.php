<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('solutions', function (Blueprint $table) {
            $table->foreignId('expert_id')->constrained('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('solutions', function (Blueprint $table) {
            $table->dropForeign(['expert_id']);
            $table->dropColumn('expert_id');
        });
    }

};
