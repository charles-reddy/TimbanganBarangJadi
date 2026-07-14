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
        Schema::create('trscale_b10_corrections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('header_id');
            $table->unsignedBigInteger('detail_id');
            $table->integer('correction_number')->comment('Koreksi ke-n');
            $table->integer('old_b10_qty_karung');
            $table->integer('new_b10_qty_karung');
            $table->decimal('old_avg_per_karung', 10, 2)->nullable();
            $table->decimal('new_avg_per_karung', 10, 2)->nullable();
            $table->text('reason')->nullable()->comment('Alasan koreksi');
            $table->unsignedBigInteger('corrected_by');
            $table->dateTime('corrected_at');
            $table->string('bukti_foto_1', 255)->nullable();
            $table->string('bukti_foto_2', 255)->nullable();
            $table->string('bukti_foto_3', 255)->nullable();
            $table->timestamps();
            
            // Foreign keys
            // Note: SQL Server doesn't allow multiple cascade paths
            // So we use NO ACTION for detail_id to avoid cascade conflicts
            $table->foreign('header_id')->references('id')->on('trscale_headers')->onDelete('cascade');
            $table->foreign('detail_id')->references('id')->on('trscale_details')->onDelete('no action');
            $table->foreign('corrected_by')->references('id')->on('users')->onDelete('no action');
            
            // Indexes
            $table->index('header_id');
            $table->index('detail_id');
            $table->index('corrected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trscale_b10_corrections');
    }
};
