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
        Schema::create('paid_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Agent::class);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('continuation_of_pay_time_scope');
            $table->integer('sum_of_commissions');
            $table->string('reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paid_leaves');
    }
};
