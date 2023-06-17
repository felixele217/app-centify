<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePipedrivetokensTable extends Migration
{
    public function up()
    {
        Schema::create('pipedrive_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->longText('access_token');
            $table->longText('refresh_token');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pipedrive_tokens');
    }
}
