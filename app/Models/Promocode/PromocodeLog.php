<?php

namespace App\Models\Promocode;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromocodeLog extends Model
{
    use HasFactory;
    protected $connection = 'pgsql7';
    public function saveModel($promocode_id)
    {
        $this->promocode_id= $promocode_id;
        $this->user_id= auth()->user()? auth()->user()->id : 1;
        $this->save();
    }
}
