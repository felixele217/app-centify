<?php

declare(strict_types=1);

use App\Models\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kickers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Plan::class);
            $table->string('type');
            $table->integer('threshold_in_percent');
            $table->integer('payout_in_percent');
            $table->string('salary_type');
            $table->string('time_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kickers');
    }
};
