<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trips extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'destination',
        'duration_days',
        'date',
        'time',
        'guide',
        'image_url',
        'description',
        'price',
        'seats',
        'free_seats',
        'remark',
    ];
}
