<?php

use App\Enums\Comment\ReasonForDeletion;
use App\Models\Question;
use App\Models\User;
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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignIdFor(Question::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('comments')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->text('body');
            $table->unsignedInteger('likes_count')->default(0);
            $table->softDeletes();
            $table->foreignId('deleted_by_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->enum('deleted_reason_code', ReasonForDeletion::values())->nullable();
            $table->text('deleted_reason_comment')->nullable();
            $table->timestamps();

            $table->index(['question_id', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
