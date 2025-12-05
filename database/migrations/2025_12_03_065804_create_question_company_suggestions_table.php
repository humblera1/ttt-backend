<?php

use App\Enums\Suggestion\Status;
use App\Models\Company;
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
        Schema::create('question_company_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Question::class)->constrained();
            $table->foreignIdFor(Company::class)->constrained();
            $table->foreignIdFor(User::class, 'moderated_by_id')
                ->nullable()
                ->constrained();
            $table->integer('evidence_count')->default(0);
            $table->enum('status', Status::values())->default(Status::Pending->value);
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['question_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_company_suggestions');
    }
};
