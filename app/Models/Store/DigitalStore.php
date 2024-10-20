<?php

namespace App\Models\Store;
use App\Models\StoreLatest\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class DigitalStore extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function saveModel($data, $status)
    {
        $this->category_id = $data['category_id'];
        $this->name = $data['name'];

        $logo = (string) Str::uuid().'-'.Str::random(15).'.'.$data['logo']->getClientOriginalExtension();
        $data['logo']->move(public_path('/files/digitalStores'),$logo);
        $this->logo = $logo;
        $this->save();
        $digitalStoreHistory = new DigitalStoreHistory();
        $digitalStoreHistory->saveModel($this,$data,$status);
    }
    public function updateModel($data, $status)
    {
        $this->category_id = $data['category_id'];
        $this->name = $data['name'];
        
        if(isset($data['logo']) && file_exists(public_path('/files/digitalStores/'.$this->logo))){
            unlink(public_path('/files/digitalStores/'.$this->logo));
            
        }
        if(isset($data['logo'])){
            $logo = (string) Str::uuid().'-'.Str::random(15).'.'.$data['logo']->getClientOriginalExtension();
            $data['logo']->move(public_path('/files/digitalStores'),$logo);
            $this->logo = $logo;
        }
        $this->save();
        $digitalStoreHistory = new DigitalStoreHistory();
        $digitalStoreHistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data, $status)
    {
        $digitalStoreHistory = new DigitalStoreHistory();
        $digitalStoreHistory->saveModel($this,$data,$status);
        if(file_exists(public_path('/files/digitalStores/'.$this->logo))){
            unlink(public_path('/files/digitalStores/'.$this->logo));
        }
        $this->delete();
    }
}
