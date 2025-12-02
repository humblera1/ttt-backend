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
        Schema::table('statistics', function (Blueprint $table) {
            $table->unique(['user_id', 'question_id', 'company_id', 'position_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->dropForeign('statistics_company_id_foreign');
            $table->dropForeign('statistics_position_id_foreign');
            $table->dropForeign('statistics_question_id_foreign');
            $table->dropForeign('statistics_user_id_foreign');
        });

        Schema::table('statistics', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'question_id', 'company_id', 'position_id']);
        });

        Schema::table('statistics', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('question_id', 'statistics_question_id_foreign')
                ->references('id')
                ->on('questions');

            $table->foreign('company_id', 'statistics_company_id_foreign')
                ->references('id')
                ->on('companies')
                ->nullOnDelete();

            $table->foreign('position_id', 'statistics_position_id_foreign')
                ->references('id')
                ->on('positions')
                ->nullOnDelete();
        });
    }
};
