<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileVersion extends Model
{
    use HasFactory;
    public function saveModel($data){
        $test['android'] = $data['android'];
        $test['ios'] = $data['ios'];
        $this->versions = json_encode($test);
        $this->save();
    }
}
