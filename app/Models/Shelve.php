<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelve extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_edition_id',
        'status',
        'times_read',
        'favourite',
        'notes',
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
