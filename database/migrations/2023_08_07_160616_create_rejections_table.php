<?php

declare(strict_types=1);

use App\Models\Deal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rejections', function (Blueprint $table) {
            $table->id();
            $table->longText('reason');
            $table->boolean('is_permanent');
            $table->foreignIdFor(Deal::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rejections');
    }
};
