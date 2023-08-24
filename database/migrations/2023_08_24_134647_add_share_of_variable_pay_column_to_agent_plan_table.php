<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_plan', function (Blueprint $table) {
            $table->integer('share_of_variable_pay')->default(100);
        });
    }

    public function down(): void
    {
        Schema::table('agent_plan', function (Blueprint $table) {
            $table->integer('share_of_variable_pay');
        });
    }
};
