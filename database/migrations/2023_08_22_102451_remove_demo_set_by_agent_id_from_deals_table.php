<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn('demo_set_by_agent_id');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            //
        });
    }
};
