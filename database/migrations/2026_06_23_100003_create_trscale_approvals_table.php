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
        Schema::create('trscale_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('header_id')->comment('FK ke trscale_headers');

            // Approval Action
            $table->string('action', 20)->comment('APPROVED atau REJECTED');
            $table->integer('approved_by')->nullable()->comment('User ID yang melakukan approval');
            $table->string('approved_by_name')->nullable()->comment('Nama user untuk audit trail');
            $table->text('approval_note')->nullable()->comment('Catatan/alasan approval atau rejection');
            $table->datetime('approved_at')->nullable();

            // Out of Range Details (JSON)
            $table->text('out_of_range_products')->nullable()->comment('JSON array berisi detail produk yang out of range');

            // Metadata
            $table->timestamp('created_at')->useCurrent();

            // Foreign Key
            $table->foreign('header_id')
                ->references('id')
                ->on('trscale_headers')
                ->onDelete('cascade');

            // Indexes
            $table->index('header_id');
            $table->index('approved_by');
            $table->index('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trscale_approvals');
    }
};
