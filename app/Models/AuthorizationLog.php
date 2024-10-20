<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorizationLog extends Model
{
    use HasFactory;
    protected $table='authorization_logs';
}
