<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\BookFormat;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_editions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('edition_title');
            $table->date('edition_publication_date')->nullable();
            $table->enum('format', array_column(BookFormat::cases(), 'value'));
            $table->string('edition_language');
            $table->text('description')->nullable();
            $table->string('isbn')->nullable();
            $table->integer('page_count')->nullable();
            $table->integer('length_minutes')->nullable();
            $table->string('cover_url')->nullable();
            $table->string('publisher')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_editions');
    }
};
