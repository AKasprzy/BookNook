<?php

use App\Enums\BookStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_edition_id')->constrained('book_editions')->cascadeOnDelete();
            $table->enum('status', array_column(BookStatus::cases(), 'value'));
            $table->integer('times_read')->default(0);
            $table->boolean('favourite')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'book_edition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shelves');
    }
};
