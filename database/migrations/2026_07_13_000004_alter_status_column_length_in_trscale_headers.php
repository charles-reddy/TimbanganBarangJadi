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
        // SQL Server: Drop dependencies first (index, then constraint), alter column, then recreate

        // 1. Drop index if exists
        $indexExists = DB::selectOne("
            SELECT name 
            FROM sys.indexes 
            WHERE name = 'trscale_headers_status_index' 
            AND object_id = OBJECT_ID('trscale_headers')
        ");

        if ($indexExists) {
            DB::statement('DROP INDEX trscale_headers_status_index ON trscale_headers');
        }

        // 2. Drop default constraint
        $constraintName = DB::selectOne("
            SELECT dc.name 
            FROM sys.default_constraints dc
            INNER JOIN sys.columns c ON dc.parent_object_id = c.object_id AND dc.parent_column_id = c.column_id
            WHERE OBJECT_NAME(dc.parent_object_id) = 'trscale_headers' AND c.name = 'status'
        ")?->name;

        if ($constraintName) {
            DB::statement("ALTER TABLE trscale_headers DROP CONSTRAINT [{$constraintName}]");
        }

        // 3. Alter column to increase length
        DB::statement('ALTER TABLE trscale_headers ALTER COLUMN status VARCHAR(30) NOT NULL');

        // 4. Re-create default constraint
        DB::statement("ALTER TABLE trscale_headers ADD CONSTRAINT DF_trscale_headers_status DEFAULT 'PENDING' FOR status");

        // 5. Re-create index
        DB::statement('CREATE INDEX trscale_headers_status_index ON trscale_headers(status)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index
        $indexExists = DB::selectOne("
            SELECT name 
            FROM sys.indexes 
            WHERE name = 'trscale_headers_status_index' 
            AND object_id = OBJECT_ID('trscale_headers')
        ");

        if ($indexExists) {
            DB::statement('DROP INDEX trscale_headers_status_index ON trscale_headers');
        }

        // Drop the constraint
        $constraintName = DB::selectOne("
            SELECT dc.name 
            FROM sys.default_constraints dc
            INNER JOIN sys.columns c ON dc.parent_object_id = c.object_id AND dc.parent_column_id = c.column_id
            WHERE OBJECT_NAME(dc.parent_object_id) = 'trscale_headers' AND c.name = 'status'
        ")?->name;

        if ($constraintName) {
            DB::statement("ALTER TABLE trscale_headers DROP CONSTRAINT [{$constraintName}]");
        }

        // Rollback to original length (WARNING: may truncate data if longer values exist)
        DB::statement('ALTER TABLE trscale_headers ALTER COLUMN status VARCHAR(20) NOT NULL');

        // Re-create default constraint
        DB::statement("ALTER TABLE trscale_headers ADD DEFAULT 'PENDING' FOR status");

        // Re-create index
        DB::statement('CREATE INDEX trscale_headers_status_index ON trscale_headers(status)');
    }
};
