<?php

use App\Models\Agent;
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
            $table->bigInteger('integration_deal_id');
            $table->string('title');
            $table->string('status');
            $table->string('owner_email');
            $table->bigInteger('value');
            $table->timestampTz('add_time');
            $table->timestampTz('accepted_at')->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->foreignIdFor(Agent::class);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
