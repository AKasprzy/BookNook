<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property int|null $rating
 * @property string|null $review_text
 * @property bool $spoiler
 * @property bool $reread
 * @property Carbon $reviewed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'book_edition_id',
        'rating',
        'review_text',
        'spoiler',
        'reread',
        'reviewed_at',
    ];

    protected $casts = [
        'spoiler' => 'boolean',
        'reread' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookEdition()
    {
        return $this->belongsTo(BookEdition::class);
    }
}
