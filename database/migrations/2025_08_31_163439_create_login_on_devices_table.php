<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('login_on_devices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('mobileNumber');
            $table->tinyText('deviceId');
            $table->tinyText('deviceName')->nullable();
            $table->tinyText('deviceModelNo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_on_devices');
    }
};
