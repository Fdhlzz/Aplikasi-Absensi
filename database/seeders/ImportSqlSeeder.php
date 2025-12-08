<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportSqlSeeder extends Seeder
{
    public function run(): void
    {
        // Path to your SQL file
        $sqlPath = database_path('seeders/sql/database.sql');

        // Read the SQL content
        $sql = File::get($sqlPath);

        // Execute SQL queries
        DB::unprepared($sql);

        echo "SQL file imported successfully!\n";
    }

}
