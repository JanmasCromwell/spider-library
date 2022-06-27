<?php

namespace App\Model;

class User extends Model
{
    const UPDATED_AT = '';

    protected $table = 'user';

    public $timestamps = false;
    protected $fillable = [
        'avatar',
        'username',
        'openid',
        'create_at',
        'mobile',
        'password'
    ];

    static function generateUserName($length = 8)
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $strlen = strlen($str) - 1;
        $end = '';
        while (strlen($end) < $length) {
            $end .= $str[rand(0, $strlen)];
        }

        return $end;
    }

    static function generatePasswd($length = 15)
    {
        return self::generateUserName($length);
    }

    static function existsOpenid(string $openid)
    {
        return self::query()->where('openid', $openid)->count() > 0;
    }
}