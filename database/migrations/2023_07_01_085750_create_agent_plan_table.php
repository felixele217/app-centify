<?php

use App\Models\Agent;
use App\Models\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Agent::class);
            $table->foreignIdFor(Plan::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_plan');
    }
};
