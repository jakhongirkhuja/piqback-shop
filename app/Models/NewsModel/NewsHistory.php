<?php

namespace App\Models\NewsModel;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql9';
    public function saveModel($model, $data, $status)
    {
        $this->title = $model->title;
        $this->banner = $model->banner;
        $this->content = $model->content;
        $this->postedDate = $model->postedDate;
        $this->moderated = auth()->user()->id;
        $this->save();
        StandardAttributes::setSA('news_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql9');
    }
}
