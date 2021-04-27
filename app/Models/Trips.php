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
        'duration',
        'date',
        'guide',
        'image',
        'description',
        'price',
        'seats',
        'freeseats',
        'remark',
    ];
}