<?php

declare(strict_types=1);

use App\Models\Agent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Agent::class);
            $table->string('time_scope');
            $table->timestampTz('date_in_scope');
            $table->integer('sick_days');
            $table->integer('vacation_days');
            $table->integer('kicker_commission');
            $table->integer('absence_commission');
            $table->integer('commission_from_quota');
            $table->integer('quota_attainment_percentage');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
