<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $fillable = [
        'name',
        'primary_choice',
        'source'
    ];

    public function companyProfiles()
    {
        return $this->belongsToMany(CompanyProfile::class);
    }
}
