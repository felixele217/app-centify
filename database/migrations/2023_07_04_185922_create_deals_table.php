<?php

use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('integration_type');
            $table->integer('integration_id');
            $table->string('title');
            $table->string('status');
            $table->integer('target_amount');
            $table->timestampTz('add_time');
            $table->timestamps();
            $table->foreignIdFor(Agent::class);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
