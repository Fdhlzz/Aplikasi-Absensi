<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perangkat', function (Blueprint $table) {
            // Stores the ID the device needs to enroll next. Null means no command.
            $table->integer('pending_enrollment_id')->nullable()->default(null)->after('api_key');
        });
    }

    public function down(): void
    {
        Schema::table('perangkat', function (Blueprint $table) {
            $table->dropColumn('pending_enrollment_id');
        });
    }
};