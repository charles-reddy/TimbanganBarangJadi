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
        Schema::create('trscale_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('header_id')->comment('FK ke trscale_headers');

            // SPM & SPPB Reference
            $table->integer('spm_id')->nullable()->comment('FK ke createspm');
            $table->integer('sppb_id')->nullable()->comment('FK ke createsppb');

            // Product Info
            $table->string('itemCode')->nullable();
            $table->string('itemName')->nullable();
            $table->string('itemType')->nullable();

            // Quantity
            $table->integer('qty_karung')->nullable()->comment('Jumlah karung/kemasan');

            // Weight Standard (dari products table)
            $table->decimal('weight_std', 10, 2)->nullable()->comment('Standar teoretis per karung dari master product');
            $table->decimal('gross_min', 10, 2)->nullable()->comment('Batas minimum per karung untuk validasi');
            $table->decimal('gross_max', 10, 2)->nullable()->comment('Batas maximum per karung untuk validasi');

            // Calculated Weights
            $table->decimal('theoretical_weight', 10, 2)->nullable()->comment('qty_karung × weight_std');
            $table->decimal('actual_weight', 10, 2)->nullable()->comment('Hasil distribusi menggunakan correction factor');
            $table->decimal('avg_per_karung', 10, 2)->nullable()->comment('actual_weight / qty_karung');

            // Validation
            $table->boolean('is_in_range')->nullable()->comment('1=dalam range (gross_min - gross_max), 0=di luar range');
            $table->boolean('need_approval')->default(false)->comment('Khusus produk ini perlu approval?');

            // Others
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Foreign Key
            $table->foreign('header_id')
                ->references('id')
                ->on('trscale_headers')
                ->onDelete('cascade');

            // Indexes
            $table->index('header_id');
            $table->index('spm_id');
            $table->index('itemCode');
            $table->index('need_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trscale_details');
    }
};
