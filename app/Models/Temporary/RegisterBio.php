<?php

namespace App\Models\Temporary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterBio extends Model
{
    use HasFactory;
    protected $connection = 'pgsql6';
}
