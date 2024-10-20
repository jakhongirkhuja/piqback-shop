<?php

namespace App\Models\Lessons;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class LessonExtraMaterial extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    public function saveModel($data, $status)
    {
        $this->lesson_id = $data['lesson_id'];
        $this->documentName= $data['documentName'];
        $documentNewName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['document']->getClientOriginalExtension();
        $data['document']->move(public_path('/files/lessonExtra'),$documentNewName);
        $this->documentURL = $documentNewName;
        $fileSize = \File::size(public_path('/files/lessonExtra/'.$documentNewName));
        $this->documentSize =  round($fileSize / 1024,4);
        $this->save();
        $lessonExtraMaterialHistory = new LessonExtraMaterialHistories();
        $lessonExtraMaterialHistory->saveModel($this,$data,$status);
    }
    public function updateModel($data, $status)
    {
        $this->lesson_id = $data['lesson_id'];
        $this->documentName= $data['documentName'];
        
        if(isset($data['document']) && file_exists(public_path('/files/lessonExtra/'.$this->documentURL))){
            unlink(public_path('/files/lessonExtra/'.$this->documentURL));
            
        }
        if(isset($data['document'])){
            $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['document']->getClientOriginalExtension();
            $data['document']->move(public_path('/files/lessonExtra'),$bannerName);
            $this->documentURL = $bannerName;
            $fileSize = \File::size(public_path('/files/lessonExtra/'.$bannerName));
            $this->documentSize =  round($fileSize / 1024,4);
        }
        
        $this->save();
        $lessonExtraMaterialHistories = new LessonExtraMaterialHistories();
        $lessonExtraMaterialHistories->saveModel($this,$data,$status);
    }
    public function deleteModel($data, $status)
    {
        $lessonExtraMaterialHistories = new LessonExtraMaterialHistories();
        $lessonExtraMaterialHistories->saveModel($this,$data,$status);
        if(file_exists(public_path('/files/lessonExtra/'.$this->documentURL))){
            unlink(public_path('/files/lessonExtra/'.$this->documentURL));
        }
        $this->delete();
    }
}
