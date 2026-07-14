<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trscale_headers', function (Blueprint $table) {
            // B10 tracking fields
            $table->unsignedBigInteger('b10_input_by')->nullable()->after('user_out_id')->comment('User ID yang input B10');
            $table->dateTime('b10_input_at')->nullable()->after('b10_input_by')->comment('Tanggal input B10');
            $table->boolean('needs_b10_correction')->default(false)->after('need_approval')->comment('Flag jika perlu koreksi B10');
            $table->boolean('correction_submitted')->default(false)->after('needs_b10_correction')->comment('Flag jika sudah submit koreksi untuk approval');
            
            // Foreign key
            $table->foreign('b10_input_by')->references('id')->on('users')->onDelete('set null');
        });

        // SQL Server uses VARCHAR for status, no need to modify column type
        // The new status values can be used directly with VARCHAR
        // Status values: WEIGHING_IN, READY_FOR_WEIGH_OUT, WEIGHING_OUT, PENDING_B10_CORRECTION, PENDING_APPROVAL, APPROVED, REJECTED, COMPLETED
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trscale_headers', function (Blueprint $table) {
            $table->dropForeign(['b10_input_by']);
            
            $table->dropColumn([
                'b10_input_by',
                'b10_input_at',
                'needs_b10_correction',
                'correction_submitted',
            ]);
        });

        // Status column remains as VARCHAR, no rollback needed
    }
};
