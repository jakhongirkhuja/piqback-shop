<?php

namespace App\Models\StoreLatest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($data, $status)
    {
        $test['ru'] = $data['name_ru'];
        $test['uz'] = $data['name_uz'];
        $this->name = json_encode($test);
        $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['icon']->getClientOriginalExtension();
        $data['icon']->move(public_path('/files/storeCategory'),$bannerName);
        $this->icon = $bannerName;
        $this->global_type = $data['global_type'];
        $this->save();
        $storecateogryhistory = new CategorieHistory();
        $storecateogryhistory->saveModel($this,$data,$status);
    }
    public function updateModel($data, $status)
    {
        $test['ru'] = $data['name_ru'];
        $test['uz'] = $data['name_uz'];
        $this->name = json_encode($test);
        
        if(isset($data['icon']) && file_exists(public_path('/files/storeCategory/'.$this->icon))){
            unlink(public_path('/files/storeCategory/'.$this->icon));
            
        }
        if(isset($data['icon'])){
            $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['icon']->getClientOriginalExtension();
            $data['icon']->move(public_path('/files/storeCategory'),$bannerName);
            $this->icon = $bannerName;
        }
        $this->global_type = $data['global_type'];
        $this->save();
        $storecateogryhistory = new CategorieHistory();
        $storecateogryhistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data, $status)
    {
        $storecateogryhistory = new CategorieHistory();
        $storecateogryhistory->saveModel($this,$data,$status);
        if(file_exists(public_path('/files/storeCategory/'.$this->icon))){
            unlink(public_path('/files/storeCategory/'.$this->icon));
        }
        $this->delete();
    }
}
