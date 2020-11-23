<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'position_id',
        'average_price',
        'average_price_converted',
        'current_price',
        'value',
        'investment',
        'margin',
        'ppl',
        'quantity',
        'active',
        'last_held',
        'ticker_212',
        'company_id',
        'portfolio_snapshot_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function portfolioSnapshot()
    {
        return $this->belongsTo(PortfolioSnapshot::class);
    }
}
