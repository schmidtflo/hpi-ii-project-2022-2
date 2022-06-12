<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }
}
