<?php

use App\Models\Admin;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePipedriveConfigsTable extends Migration
{
    public function up()
    {
        Schema::create('pipedrive_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class);
            $table->longText('access_token');
            $table->longText('refresh_token');
            $table->timestamp('expires_at');
            $table->string('subdomain')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pipedrive_configs');
    }
}
