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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('label')->nullable()->change();
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->unique(['section', 'key'], 'uniq_section_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('uniq_section_key');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('label')->nullable(false)->change();
        });
    }
};
