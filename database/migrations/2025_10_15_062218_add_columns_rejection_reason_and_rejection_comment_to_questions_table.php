<?php

use App\Enums\QuestionRejectionReason;
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
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('rejection_reason', QuestionRejectionReason::values())
                ->nullable()
                ->after('status');

            $table->text('rejection_comment')
                ->nullable()
                ->after('rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'rejection_comment']);
        });
    }
};
