<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            if (DB::connection()->getDriverName() !== 'sqlite') {
                $table->dropForeign('deals_deal_won_by_agent_id_foreign');
            }

            $table->dropColumn('deal_won_by_agent_id');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            //
        });
    }
};
