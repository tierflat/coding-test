<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeyValue extends Model
{
    use HasFactory;
    protected $table = 'key_value';
    protected $fillable = ['key', 'value'];
}
