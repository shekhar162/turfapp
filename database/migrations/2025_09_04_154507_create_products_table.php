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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category_id')->constrained()->onDelete('cascade')->default(1);
            $table->bigInteger('mobileNumber');
            $table->string('closedDays')->nullable();
            $table->string('closedHrs')->default('12-1,1-2,2-3,3-4,4-5');
            $table->string('suitableFor')->nullable();
            $table->longText('otherFacility')->nullable();
            $table->enum('status', ['1', '2']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turfs');
    }
};
