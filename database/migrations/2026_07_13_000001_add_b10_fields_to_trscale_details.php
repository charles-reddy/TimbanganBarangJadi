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
        Schema::table('trscale_details', function (Blueprint $table) {
            // Input B10 fields
            $table->integer('b10QtyKarung')->nullable()->after('qty_karung')->comment('Qty karung actual dari B10');
            $table->string('b10BatchNo', 50)->nullable()->after('b10QtyKarung')->comment('Batch number dari B10');
            $table->string('kontainerNo', 50)->nullable()->after('b10BatchNo')->comment('Container number');
            $table->string('krani', 100)->nullable()->after('kontainerNo')->comment('Nama krani yang input');
            $table->string('imgFormLoading', 255)->nullable()->after('krani')->comment('Path foto form loading');
            
            // Correction tracking fields
            $table->integer('b10QtyKarung_original')->nullable()->after('imgFormLoading')->comment('Qty karung original sebelum koreksi');
            $table->integer('b10_correction_count')->default(0)->after('b10QtyKarung_original')->comment('Jumlah koreksi yang dilakukan');
            $table->unsignedBigInteger('b10_corrected_by')->nullable()->after('b10_correction_count')->comment('User ID yang koreksi');
            $table->dateTime('b10_corrected_at')->nullable()->after('b10_corrected_by')->comment('Tanggal koreksi');
            $table->string('buktiKoreksi1', 255)->nullable()->after('b10_corrected_at')->comment('Foto bukti koreksi 1');
            $table->string('buktiKoreksi2', 255)->nullable()->after('buktiKoreksi1')->comment('Foto bukti koreksi 2');
            $table->string('buktiKoreksi3', 255)->nullable()->after('buktiKoreksi2')->comment('Foto bukti koreksi 3');
            
            // Foreign key
            $table->foreign('b10_corrected_by')->references('id')->on('users')->onDelete('set null');
            
            // Index untuk performance
            $table->index('isLoadingDone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trscale_details', function (Blueprint $table) {
            $table->dropForeign(['b10_corrected_by']);
            $table->dropIndex(['isLoadingDone']);
            
            $table->dropColumn([
                'b10QtyKarung',
                'b10BatchNo',
                'kontainerNo',
                'krani',
                'imgFormLoading',
                'b10QtyKarung_original',
                'b10_correction_count',
                'b10_corrected_by',
                'b10_corrected_at',
                'buktiKoreksi1',
                'buktiKoreksi2',
                'buktiKoreksi3',
            ]);
        });
    }
};
