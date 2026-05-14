<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $title
 * @property string $original_language
 * @property string $author
 * @property string|null $series
 * @property Carbon|null $original_publication_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'original_language',
        'author',
        'original_publication_date',
        'series',
    ];

    protected function casts(): array
    {
        return [
            'original_publication_date' => 'date',
        ];
    }

    public function editions(): HasMany
    {
        return $this->hasMany(BookEdition::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'book_genre');
    }

    public function motifs()
    {
        return $this->belongsToMany(Motif::class, 'book_motif');
    }
}
