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
        Schema::create('trscale', function (Blueprint $table) {
            $table->id();
            $table->string('driver');
            $table->integer('custID');
            $table->string('custName');
            $table->integer('transpID');
            $table->string('transpName');
            $table->string('itemCode');
            $table->string('itemName');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trscale');
    }
};
