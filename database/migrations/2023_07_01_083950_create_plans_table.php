<?php

use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->integer('target_amount_per_month');
            $table->string('target_variable');
            $table->string('payout_frequency');
            $table->foreignIdFor(User::class, 'creator_id');
            $table->foreignIdFor(Organization::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
