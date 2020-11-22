<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'isin',
        'ticker_212',
        'ric',
        'exchange',
        'ticker'
    ];

    public function profiles()
    {
        return $this->hasMany(CompanyProfile::class);
    }

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
}
