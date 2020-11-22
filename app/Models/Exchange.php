<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $fillable = [
        'mic',
        'name',
        'operating_mic',
        'acronym',
        'creation_date',
        'city',
        'country',
        'status',
        'comment',
        'website'
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
