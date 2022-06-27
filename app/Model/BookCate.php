<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Model;

class BookCate extends Model
{
    const SHOW   = 1;
    const HIDDEN = 0;

    protected $table      = 'book_cates';
    public    $timestamps = false;
    protected $fillable   = [
        'name',
        'status',
        'create_time'
    ];

    static function isExists(string $name)
    {
        return self::query()->where('name', '=', $name)->count() > 0;
    }

}
