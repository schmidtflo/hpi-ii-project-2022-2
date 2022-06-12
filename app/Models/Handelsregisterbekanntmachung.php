<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Handelsregisterbekanntmachung extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'handelsregisterbekanntmachungen';

    protected $casts = [
        'event_date' => 'date'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
