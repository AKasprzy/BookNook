<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_edition_id')->constrained('book_editions')->cascadeOnDelete();
            $table->tinyInteger('rating')->nullable()->unsigned();
            $table->text('review_text')->nullable();
            $table->boolean('spoiler')->default(false);
            $table->boolean('reread')->default(false);
            $table->timestamp('reviewed_at')->useCurrent();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
