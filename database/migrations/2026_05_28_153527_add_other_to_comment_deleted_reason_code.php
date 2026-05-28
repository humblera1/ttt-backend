<?php

use App\Enums\Comment\ReasonForDeletion;
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
        Schema::table('comments', function (Blueprint $table) {
            $table->enum('deleted_reason_code', ReasonForDeletion::values())
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->enum('deleted_reason_code', [
                'user_removed',
                'spam',
                'abuse',
                'offtopic',
                'duplicate',
            ])
                ->nullable()
                ->change();
        });
    }
};
