<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'book';
    protected $fillable = [
        'title',
        'author',
        'description',
        'image_uri',
        'rating',
        'download_uri',
        'download_count',
        'view_count'
    ];
}
