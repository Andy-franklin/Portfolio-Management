<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioSnapshot extends Model
{
    protected $table = 'portfolio_snapshot';

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
