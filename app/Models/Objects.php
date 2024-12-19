<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objects extends Model
{
    protected $table = 'objects';
    protected $fillable = ['key', 'value', 'timestamp', 'is_binary'];
    
    public static function getByTimestamp($key, $timestamp)
    {
        return self::where('key', $key)
            ->where('timestamp', '<=', $timestamp)
            ->orderBy('timestamp', 'desc')
            ->first();
    }
    
    public static function getLatestValue($key)
    {
        return self::where('key', $key)
            ->orderBy('timestamp', 'desc')
            ->first();
    }
}
