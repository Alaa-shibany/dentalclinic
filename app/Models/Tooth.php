<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tooth extends Model
{
    use HasFactory;
    protected $fillable = [
        'toothId',
        'order_id'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
