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
        Schema::create('createsppbs', function (Blueprint $table) {
            $table->id(); 
            $table->string('sppbNo');
            $table->dateTime('tglSppb');
            $table->integer('custID');
            $table->string('kontrakNo');
            $table->string('itemCode'); 
            $table->integer('sppbQtyKg');
            $table->integer('sppbQtyKarung');
            $table->integer('openQtyKg');
            $table->integer('openQtyKarung');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('createsppbs');
    }
};
