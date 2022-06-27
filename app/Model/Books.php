<?php

namespace App\Model;

class Books extends Model
{
    protected $table = 'books';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'link',
        'cover',
        'create_time',
        'etag',
        'size',
        'ext',
        'author',
        'year',
        'lang',
        'mime'
    ];

    static function isExists(string $name)
    {
        return self::query()->where('name', '=', $name)->count() > 0;
    }

    static function isLikeExists(string $name)
    {
        return self::query()->where('name', 'like', "%{$name}%")->count() > 0;
    }
}
