<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreObjectCodeCheck extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
}
