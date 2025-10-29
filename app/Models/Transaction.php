<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'description',
        'date',
        'type',
        'qty',
        'price',
        'cost',
        'total_cost',
        'qty_balance',
        'value_balance',
        'hpp',
    ];

    protected $casts = [
        'price' => 'float',
        'cost' => 'float',
        'total_cost' => 'float',
        'value_balance' => 'float',
        'hpp' => 'float',
    ];
}
