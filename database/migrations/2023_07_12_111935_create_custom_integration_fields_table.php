<?php

declare(strict_types=1);

use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_integration_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('api_key');
            $table->string('integration_type');
            $table->foreignIdFor(Organization::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_integration_fields');
    }
};