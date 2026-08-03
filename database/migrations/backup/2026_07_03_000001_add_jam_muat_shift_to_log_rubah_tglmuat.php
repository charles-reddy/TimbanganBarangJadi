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
        Schema::connection('sqlsrv')->table('tbl_log_rubah_tglMuat', function (Blueprint $table) {
            if (!Schema::connection('sqlsrv')->hasColumn('tbl_log_rubah_tglMuat', 'jamMuat')) {
                $table->time('jamMuat')->nullable()->after('tglMuat');
            }
            if (!Schema::connection('sqlsrv')->hasColumn('tbl_log_rubah_tglMuat', 'jamMuat1')) {
                $table->time('jamMuat1')->nullable()->after('tglMuat1');
            }
            if (!Schema::connection('sqlsrv')->hasColumn('tbl_log_rubah_tglMuat', 'shift')) {
                $table->string('shift', 50)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sqlsrv')->table('tbl_log_rubah_tglMuat', function (Blueprint $table) {
            $table->dropColumn(['jamMuat', 'jamMuat1', 'shift']);
        });
    }
};
