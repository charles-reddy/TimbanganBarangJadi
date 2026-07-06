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
            $table->time('jamMuat')->nullable()->after('tglMuat');
            $table->time('jamMuat1')->nullable()->after('tglMuat1');
            $table->string('shift', 50)->nullable()->after('jamMuat1');
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
