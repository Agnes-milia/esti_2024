<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $primaryKey = 'book_id';

    protected $fillable = [
        'author',
        'title',
        'pieces',   
    ];

    public function booksToCopies(){
        return $this->hasMany(Copy::class, 'book_id', 'book_id');
    }
}
