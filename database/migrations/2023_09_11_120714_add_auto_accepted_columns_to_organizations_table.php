<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->boolean('auto_accept_demo_scheduled')->default(false);
            $table->boolean('auto_accept_deal_won')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->removeColumn('auto_accept_demo_scheduled');
            $table->removeColumn('auto_accept_deal_won');
        });
    }
};
