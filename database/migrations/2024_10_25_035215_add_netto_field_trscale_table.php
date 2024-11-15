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
            $table->integer('car')->after('itemName')->nullable();
            $table->integer('netto')->after('itemName')->nullable();
            $table->integer('gross')->after('itemName')->nullable();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trscale',function(Blueprint $table) {
            $table->dropColumn('car');
            $table->dropColumn('netto');
            $table->dropColumn('gross');
        });
    }
};
