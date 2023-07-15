<?php

use App\Models\Admin;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('target_amount_per_month');
            $table->string('target_variable');
            $table->string('payout_frequency');
            $table->foreignIdFor(Admin::class, 'creator_id');
            $table->foreignIdFor(Organization::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
