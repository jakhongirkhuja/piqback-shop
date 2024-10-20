<?php

namespace App\Models\Lessons;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LessonContent extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    protected $cast = [
        'contentOrder'=>'integer',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'lesson_id',
        
    ];
    public function saveModel($id, $data, $dec)
    {
        
        $this->lesson_id = $id;
        $this->type = $dec->type;
        $this->contentOrder = $dec->contentOrder;
        $test['uz'] = '';
        if(isset($dec->body_uz)){
            $test['uz'] = $dec->body_uz;
        }
        if(isset($dec->body_ru)){
            $test['ru'] = $dec->body_ru;
        }
        $this->body = json_encode($test);
        $this->save();
        $lessonContentHistory  = new LessonContentHistory();
        $lessonContentHistory->saveModel($this, $data);
    }

    public function saveImgModel($id, $data)
    {
        
        $this->lesson_id = $id;
        $this->type = $data['type'];
        $this->contentOrder = $data['contentOrder'];
        if(isset($data['body_ru']) && $data['body_ru']){
            $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['body_ru']->getClientOriginalExtension();
            $data['body_ru']->move(public_path('/files/lessons'),$bannerName);
            $bannertest['ru'] = $bannerName;
        }
        if(isset($data['body_uz']) && $data['body_uz']){
            $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['body_uz']->getClientOriginalExtension();
            $data['body_uz']->move(public_path('/files/lessons'),$bannerName);
            $bannertest['uz'] = $bannerName;
        }
        
        $this->body = json_encode($bannertest);
        $this->save();
        $lessonContentHistory  = new LessonContentHistory();
        $lessonContentHistory->saveModel($this, $data);
    }

    public function updateImgModel($data)
    {
        
        $categoryIcon = json_decode($this->body);
        if(isset( $categoryIcon->ru)){
            $bannertest['ru'] = $categoryIcon->ru;
        }
        if(isset( $categoryIcon->uz)){
            $bannertest['uz'] = $categoryIcon->uz;
        }
        if((isset($data['body_ru']) && $data['body_ru']) || (isset($data['body_uz']) && $data['body_uz'])){
            if(isset($data['body_ru']) && $data['body_ru']){
                $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['body_ru']->getClientOriginalExtension();
                $data['body_ru']->move(public_path('/files/lessons'),$bannerName);
                $bannertest['ru'] = $bannerName;
            }
            if(isset($data['body_uz']) && $data['body_uz']){
                $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['body_uz']->getClientOriginalExtension();
                $data['body_uz']->move(public_path('/files/lessons'),$bannerName);
                $bannertest['uz'] = $bannerName;
            }
            $this->body = json_encode($bannertest);
        }
        $this->save();
        $lessonContentHistory  = new LessonContentHistory();
        $lessonContentHistory->saveModel($this, $data);
    }
    
    public function updateModel($data)
    {
        $body  = json_decode($this->body);
        if(isset( $body->ru)){
            $test['ru'] = $body->ru;
        }
        if(isset( $body->uz)){
            $test['uz'] = $body->uz;
        }
        if(isset($data['body_uz']) && $data['body_uz']!=''){
          
            $test['uz'] = $data['body_uz'];
        }
        if(isset($data['body_ru']) && $data['body_ru']!=''){
           
            $test['ru'] = $data['body_ru'];
        }
        $this->body = json_encode($test);
        $this->save();
      
        $lessonContentHistory  = new LessonContentHistory();
        $lessonContentHistory->updateModel($this, $data);
    }
    public function deleteModel($data)
    {
        
        $lessonContentHistory  = new LessonContentHistory();
        $lessonContentHistory->deleteModel($this, $data);
      
        $this->delete();
       
    }
}

