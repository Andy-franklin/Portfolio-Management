<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $fillable = [
        'company_id',
        'source',
        'name',
        'employees',
        'description',

        //todo: Move to company_financials
//        'market_cap',
//        'shares_outstanding',
//        'eps',
//        'revenue',
//        'pe_ratio',
//        'dividend_yield',
//        'beta',

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sectors()
    {
        return $this->belongsToMany(Sector::class);
    }

    public function industries()
    {
        return $this->belongsToMany(Industry::class);
    }
}
