<?php

namespace App\Models\Lottery;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryLog extends Model
{
    use HasFactory;
    protected $connection = 'pgsql10';
    public function saveModel($lottery_id, $order){
        $this->lottery_id = $lottery_id;
        $this->user_id = auth()->user()->id;
        $this->ticket = md5((string)($lottery_id.auth()->user()->id));
        $this->datetime = Carbon::now();
        $this->order = $order;
        $this->save();
    }
    public function updateStatus($data){
        $this->hasWon = $data['status'];
        $this->save();
    }
}
