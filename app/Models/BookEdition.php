<?php

namespace App\Models;

use App\Enums\BookFormat;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $book_id
 * @property string $edition_title
 * @property Carbon|null $edition_publication_date
 * @property BookFormat $format
 * @property string $edition_language
 * @property string|null $description
 * @property string|null $isbn
 * @property int|null $page_count
 * @property int|null $length_minutes
 * @property string|null $cover_url
 * @property string|null $publisher
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class BookEdition extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'edition_title',
        'edition_publication_date',
        'format',
        'edition_language',
        'description',
        'isbn',
        'page_count',
        'length_minutes',
        'cover_url',
        'publisher',
    ];

    protected function casts(): array
    {
        return [
            'edition_publication_date' => 'date',
            'average_rating' => 'float',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function genres()
    {
        return $this->book->genres();
    }

    public function motifs()
    {
        return $this->book->motifs();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function shelves()
    {
        return $this->hasMany(Shelve::class);
    }
}
