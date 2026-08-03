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
        Schema::table('trscale_headers', function (Blueprint $table) {
            if (!Schema::hasColumn('trscale_headers', 'total_range_min')) {
                $table->decimal('total_range_min', 10, 2)->nullable()
                    ->after('correction_factor')
                    ->comment('Total range minimum dari semua produk (sum of qty × gross_min)');
            }
            
            if (!Schema::hasColumn('trscale_headers', 'total_range_max')) {
                $table->decimal('total_range_max', 10, 2)->nullable()
                    ->after('total_range_min')
                    ->comment('Total range maximum dari semua produk (sum of qty × gross_max)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trscale_headers', function (Blueprint $table) {
            $table->dropColumn(['total_range_min', 'total_range_max']);
        });
    }
};
