<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\Explored;
use Laravel\Scout\Searchable;

class Article extends Model implements Explored
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

    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
            'title' => [
                'type' => 'text',
                'analyzer' => 'standard'
            ],
            'content' => 'text',
            'date' => 'date',
            'url' => 'text',
        ];
    }
}
