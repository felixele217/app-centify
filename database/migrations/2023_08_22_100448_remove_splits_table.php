<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('splits');
    }

    public function down(): void
    {
        Schema::create('splits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Deal::class);
            $table->foreignIdFor(Agent::class);
            $table->integer('shared_percentage');
            $table->timestamps();
        });
    }
};
