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
        Schema::create('trscale_headers', function (Blueprint $table) {
            $table->id();

            // Transaction Info
            $table->string('trans_no', 50)->unique()->comment('Format: TRX/YYYY/MM/0001');
            $table->string('trans_type', 20)->default('MULTI')->comment('SINGLE atau MULTI');

            // Vehicle & Driver Info
            $table->string('driver')->nullable();
            $table->string('carID')->nullable()->comment('Plat nomor kendaraan');

            // Customer & Transporter
            $table->integer('custID')->nullable();
            $table->string('custName')->nullable();
            $table->integer('transpID')->nullable();
            $table->string('transpName')->nullable();

            // Document Numbers
            $table->string('doNo')->nullable()->comment('Delivery Order Number');
            $table->string('poNo')->nullable()->comment('Purchase Order Number');

            // Weighing Data
            $table->decimal('tare_weight', 10, 2)->nullable()->comment('Timbang masuk (truk kosong) dalam kg');
            $table->decimal('gross_weight', 10, 2)->nullable()->comment('Timbang keluar (truk + muatan) dalam kg');
            $table->decimal('net_weight', 10, 2)->nullable()->comment('gross_weight - tare_weight');

            // Theoretical Weight
            $table->decimal('theoretical_weight', 10, 2)->nullable()->comment('Total (qty × weight_std)');
            $table->decimal('correction_factor', 8, 6)->nullable()->comment('K = net_weight / theoretical_weight');

            // Scale Info
            $table->integer('scale_in_id')->nullable()->comment('ID timbangan masuk');
            $table->integer('scale_out_id')->nullable()->comment('ID timbangan keluar');

            // Timestamp
            $table->datetime('weigh_in_time')->nullable();
            $table->datetime('weigh_out_time')->nullable();

            // User Info
            $table->integer('user_in_id')->nullable()->comment('User yang handle timbang masuk');
            $table->integer('user_out_id')->nullable()->comment('User yang handle timbang keluar');

            // Approval Status
            $table->string('status', 20)->default('PENDING')->comment('PENDING, WEIGHING_IN, WEIGHING_OUT, PENDING_APPROVAL, APPROVED, REJECTED, COMPLETED');
            $table->boolean('need_approval')->default(false)->comment('0=tidak perlu, 1=perlu approval');
            $table->integer('approved_by')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->text('approval_note')->nullable();

            // Others
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('trans_no');
            $table->index('status');
            $table->index('need_approval');
            $table->index('created_at');
            $table->index('carID');
            $table->index('driver');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trscale_headers');
    }
};
