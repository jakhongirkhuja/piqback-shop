<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    
    protected $hidden = [
        'created_at',
        'updated_at',
        
    ];
    public function courses(){
        return $this->hasMany(Course::class);
    }
    public function saveModel($data){

        $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['categoryIcon']->getClientOriginalExtension();
        $data['categoryIcon']->move(public_path('/files/category'),$bannerName);
        // Storage::putFileAs('/public/category/',$data['categoryIcon'],$bannerName);
        $bannertest['ru'] = $bannerName;
        $this->categoryIcon = json_encode($bannertest);
        $this->access = $data['access'];

        $test['ru'] = $data['categoryName'];
        $this->categoryName = json_encode($test);
        
        $this->save();
        $categoryHistory  = new CategoryHistory();
        $categoryHistory->saveModel($this, $data);
        return $this;
    }
    public function saveModelAll($data){

        $bannerName_ru = (string) Str::uuid().'-'.Str::random(15).'.'.$data['categoryIcon_ru']->getClientOriginalExtension();
        $data['categoryIcon_ru']->move(public_path('/files/category'),$bannerName_ru);
        $bannertest['ru'] = $bannerName_ru;

        $bannerName_uz = (string) Str::uuid().'-'.Str::random(15).'.'.$data['categoryIcon_uz']->getClientOriginalExtension();
        $data['categoryIcon_uz']->move(public_path('/files/category'),$bannerName_uz);
        $bannertest['uz'] = $bannerName_uz;
        $this->categoryIcon = json_encode($bannertest);
        $test['ru'] = $data['categoryName_ru'];
        $test['uz'] = $data['categoryName_uz'];
        $this->categoryName = json_encode($test);
        $this->access = $data['access'];
        $this->save();
        $categoryHistory  = new CategoryHistory();
        $categoryHistory->saveModel($this, $data);
        return $this;
    }

    public function updateModel($data){

        
        
        if(isset($data['categoryIcon']) && $data['categoryIcon']){

            $categoryIcon = json_decode($this->categoryIcon);
            if(isset( $categoryIcon->ru)){
                $bannertest['ru'] = $categoryIcon->ru;
            }
            if(isset( $categoryIcon->uz)){
                $bannertest['uz'] = $categoryIcon->uz;
            }
            $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['categoryIcon']->getClientOriginalExtension();
            // Storage::putFileAs('/public/category/',$data['categoryIcon'],$bannerName);
            $data['categoryIcon']->move(public_path('/files/category'),$bannerName);
            if($data['language']=='uz'){
                $bannertest['uz'] = $bannerName;
            }else{
                $bannertest['ru'] = $bannerName;
            }
            $this->categoryIcon = json_encode($bannertest);
        }
        


        
        




        $categoryName  = json_decode($this->categoryName);
        if(isset( $categoryName->ru)){
            $test['ru'] = $categoryName->ru;
        }
        if(isset( $categoryName->uz)){
            $test['uz'] = $categoryName->uz;
        }
        if($data['language']=='uz'){
            $test['uz'] = $data['categoryName'];
        }else{
            $test['ru'] = $data['categoryName'];
        }
        $this->categoryName = json_encode($test);
        $this->access = $data['access'];
        $this->save();
        $categoryHistory  = new CategoryHistory();
        $categoryHistory->updateModel($this, $data);
    }
    public function updateModelAll($data){

        
        
        if((isset($data['categoryIcon_ru']) && $data['categoryIcon_ru']) || (isset($data['categoryIcon_uz']) && $data['categoryIcon_uz'])){
            $inside = false;
            $categoryIcon = json_decode($this->categoryIcon);
            if(isset( $categoryIcon->ru)){
                $bannertest['ru'] = $categoryIcon->ru;
                $inside =true;
            }
            if(isset( $categoryIcon->uz)){
                $bannertest['uz'] = $categoryIcon->uz;
                $inside =true;
            }
            if(isset($data['categoryIcon_ru']) && $data['categoryIcon_ru']){
                $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['categoryIcon_ru']->getClientOriginalExtension();
                $data['categoryIcon_ru']->move(public_path('/files/category'),$bannerName);
                $bannertest['ru'] = $bannerName;
                
            }
            if(isset($data['categoryIcon_uz']) && $data['categoryIcon_uz']){
                $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['categoryIcon_uz']->getClientOriginalExtension();
                $data['categoryIcon_uz']->move(public_path('/files/category'),$bannerName);
                $bannertest['uz'] = $bannerName;
            }
            if($inside){
                $this->categoryIcon = json_encode($bannertest);
            }
        }
        
        $test['ru'] = $data['categoryName_ru'];
        $test['uz'] = $data['categoryName_uz'];
        $this->categoryName = json_encode($test);
        $this->access = $data['access'];
        $this->save();
        $categoryHistory  = new CategoryHistory();
        $categoryHistory->updateModel($this, $data);
    }
}
