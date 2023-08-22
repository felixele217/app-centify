<?php

declare(strict_types=1);

use App\Models\Agent;
use App\Models\Deal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_deal', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Agent::class);
            $table->foreignIdFor(Deal::class);
            $table->integer('deal_percentage')->default(100);
            $table->string('triggered_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_deal');
    }
};
