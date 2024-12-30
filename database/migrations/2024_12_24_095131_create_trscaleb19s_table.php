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
        Schema::create('trscaleb19s', function (Blueprint $table) {
            $table->id();
            $table->string('driver');
            $table->string('carID');
            $table->integer('custID');
            $table->integer('transpID');
            $table->string('itemCode');
            $table->string('doNo');
            $table->string('poNo');
            $table->string('remarks');
            $table->integer('timbangout');
            $table->integer('netto');
            $table->integer('timbangin');
            $table->string('timbanganInID');
            $table->string('timbanganOutID');
            $table->integer('grossBeforeDed');
            $table->datetime('jam_in');
            $table->datetime('jam_out');
            $table->integer('userIDIN');
            $table->string('usernameIN');
            $table->integer('userIDOUT');
            $table->string('usernameOUT');
            $table->boolean('isB19');
            $table->integer('isB19ID');
            $table->datetime('isB19Date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trscaleb19s');
    }
};
