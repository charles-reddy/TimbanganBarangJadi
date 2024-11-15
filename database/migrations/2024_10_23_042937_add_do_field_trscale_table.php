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
        Schema::table('trscale',function(Blueprint $table) {
            $table->integer('doNo')->after('itemName');
            $table->string('poNo')->after('itemName');
            $table->string('remarks')->after('itemName');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trscale',function(Blueprint $table) {
            $table->dropColumn('doNo');
            $table->dropColumn('poNo');
            $table->dropColumn('remarks');
        });
    }
};
