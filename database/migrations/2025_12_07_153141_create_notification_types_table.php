<?php

use App\Models\NotificationCategory;
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
        Schema::create('notification_types', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(NotificationCategory::class)->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('template_title')->nullable();
            $table->text('template_body')->nullable();
            $table->json('placeholders')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['notification_category_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_types');
    }
};
