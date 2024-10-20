<?php

namespace App\Models\Money;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql4';
}
