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
        Schema::create('createspms', function (Blueprint $table) {
            $table->id();
            $table->string('spmNo');
            $table->string('sppbNo');
            $table->string('regNo');
            $table->dateTime('tglSpm');
            $table->string('itemCode'); 
            $table->integer('transpID');
            $table->integer('custID');
            $table->integer('qtyKarung');
            $table->string('terbilangkarung');
            $table->integer('qtyKg');
            $table->string('terbilangKg');
            $table->string('carID');
            $table->string('driver');
            $table->string('remarks')->nullable();
            $table->string('sealNo')->nullable();
            $table->string('kontainerNo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('createspms');
    }
};
