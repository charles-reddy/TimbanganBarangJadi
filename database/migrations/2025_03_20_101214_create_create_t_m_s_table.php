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
        Schema::create('create_t_m_s', function (Blueprint $table) {
            $table->id();
            $table->string('sppbNo');
            $table->string('pendfNo');
            $table->datetime('tglDaftar');
            $table->string('carID');
            $table->string('driver');
            $table->string('noHPDriver');
            $table->string('jenisTruk');
            $table->integer('transpID');
            $table->integer('qtyKarung');
            $table->string('terbilangkarung');
            $table->integer('qtyKg');
            $table->string('terbilangKg');
            $table->string('simKtp');
            $table->string('stnk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('create_t_m_s');
    }
};
