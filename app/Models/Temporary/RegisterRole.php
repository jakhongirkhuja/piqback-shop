<?php

namespace App\Models\Temporary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterRole extends Model
{
    use HasFactory;
    protected $connection = 'pgsql6';
}
