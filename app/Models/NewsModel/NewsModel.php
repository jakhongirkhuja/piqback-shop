<?php

namespace App\Models\NewsModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsModel extends Model
{
    protected $table = 'news';
    protected $connection = 'pgsql9';
    use HasFactory;
    public function saveModel($data)
    {
        $test['uz'] = $data['title_uz'];
        $test['ru'] = $data['title_ru'];
        $this->title = json_encode($test);

        $banner['uz'] = $data['content_uz'];
        $banner['ru'] = $data['content_ru'];
        $this->content = json_encode($banner);
        $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['banner']->getClientOriginalExtension();
        $data['banner']->move(public_path('/files/news'),$bannerName);
        $this->banner = $bannerName;
        $this->postedDate = $data['postedDate'];
        $this->save();
        $newsHistory  = new NewsHistory();
        $newsHistory->saveModel($this, $data,'created');
    }
    public function updateModel($data)
    {
        $test['uz'] = $data['title_uz'];
        $test['ru'] = $data['title_ru'];
        $this->title = json_encode($test);

        $banner['uz'] = $data['content_uz'];
        $banner['ru'] = $data['content_ru'];
        $this->content = json_encode($banner);
        if(isset($data['banner']) && file_exists(public_path('/files/news/'.$this->banner))){
            unlink(public_path('/files/news/'.$this->banner));
        }
        if(isset($data['banner'])){
            $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['banner']->getClientOriginalExtension();
            $data['banner']->move(public_path('/files/news'),$bannerName);
            $this->banner = $bannerName;
        }
        $this->postedDate = $data['postedDate'];
        $this->save();
        $newsHistory  = new NewsHistory();
        $newsHistory->saveModel($this, $data,'updated');
    }
    public function deleteModel($data)
    {
        $newsHistory  = new NewsHistory();
        $newsHistory->saveModel($this, $data,'deleted');
        if(file_exists(public_path('/files/news/'.$this->banner))){
            unlink(public_path('/files/news/'.$this->banner));
        }
        $this->delete();
    }
}
