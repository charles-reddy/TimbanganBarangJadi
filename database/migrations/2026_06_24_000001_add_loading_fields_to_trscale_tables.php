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
        // Add isLoading fields to trscale_headers
        Schema::table('trscale_headers', function (Blueprint $table) {
            if (!Schema::hasColumn('trscale_headers', 'isLoading')) {
                $table->boolean('isLoading')->nullable()->after('remarks')->comment('1=sedang loading, 0/null=belum');
            }
            if (!Schema::hasColumn('trscale_headers', 'isLoadingDate')) {
                $table->dateTime('isLoadingDate')->nullable()->after('isLoading')->comment('Waktu mulai loading');
            }
        });

        // Add isLoading fields to trscale_details
        Schema::table('trscale_details', function (Blueprint $table) {
            if (!Schema::hasColumn('trscale_details', 'isLoading')) {
                $table->boolean('isLoading')->nullable()->after('remarks')->comment('1=sedang loading, 0/null=belum');
            }
            if (!Schema::hasColumn('trscale_details', 'isLoadingDate')) {
                $table->dateTime('isLoadingDate')->nullable()->after('isLoading')->comment('Waktu mulai loading');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trscale_headers', function (Blueprint $table) {
            $table->dropColumn(['isLoading', 'isLoadingDate']);
        });

        Schema::table('trscale_details', function (Blueprint $table) {
            $table->dropColumn(['isLoading', 'isLoadingDate']);
        });
    }
};
