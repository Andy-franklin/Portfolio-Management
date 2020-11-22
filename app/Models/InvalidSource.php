<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvalidSource extends Model
{
    protected $table = 'invalid_source';

    protected $fillable = [
        'url'
    ];
}