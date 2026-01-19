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
        Schema::table('system_settings', function (Blueprint $table) {
            // Add created_at column with default value
            $table->timestamp('created_at')->nullable()->after('description');
        });

        // Update existing records to have created_at = updated_at
        DB::table('system_settings')->update([
            'created_at' => DB::raw('updated_at')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('created_at');
        });
    }
};
