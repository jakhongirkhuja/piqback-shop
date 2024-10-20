<?php

namespace App\Models\Course;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    public function saveModel($category, $data){
        $this->category_id = $category->id;
        $this->categoryIcon = $category->categoryIcon;
        $this->categoryName = $category->categoryName;
        $user_id = auth()->user()? auth()->user()->id : 1;
        $this->moderated = $user_id;
        $this->access = $category->access;
        $this->save();
        StandardAttributes::setSA('category_histories',$this->id,'created',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
        return $this;
    }

    public function updateModel($model, $data){
        $this->category_id = $model->id;
        $this->categoryIcon = $model->categoryIcon;
        $this->categoryName = $model->categoryName;
        $user_id = auth()->user()? auth()->user()->id : 1;
        $this->moderated = $user_id;
        $this->access = $model->access;
        $this->save();
        StandardAttributes::setSA('category_histories',$this->id,'updated',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
        return $this;
    }
}
