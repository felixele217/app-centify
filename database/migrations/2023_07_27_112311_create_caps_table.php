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
        Schema::create('caps', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Plan::class);
            $table->bigInteger('value');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caps');
    }
};
