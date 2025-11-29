<?php

namespace App\Models;

use App\Enums\BookStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property BookStatus $status
 * @property int $times_read
 * @property bool $favourite
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Shelve extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'times_read',
        'favourite',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(BookEdition::class);
    }
}
