<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification';
    protected $fillable = [
        'type',
        'title',
        'description',
        'image_uri',
        'link_uri'
    ];
}
