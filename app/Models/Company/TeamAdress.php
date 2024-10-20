<?php

namespace App\Models\Company;

use App\Models\City;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class TeamAdress extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModelCoor($data)
    {
        $getInfoYandex = Http::get('https://geocode-maps.yandex.ru/1.x/?apikey=97cc3b95-6b0c-4891-9481-5b375bbf00fc&geocode='.(float)$data['longitude'].','.(float)$data['latitude'].'&format=json&lang=ru-RU&results=5');
        $yandexbody = $getInfoYandex->json()['response']['GeoObjectCollection']['featureMember'];
        $dataAdress = [];
        foreach ($yandexbody as  $eachbody) {
            $components = $eachbody['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['Components'];
            foreach ($components as $component) {
                switch ($component['kind']) {
                    case 'province':
                        $dataAdress['city'] = $component['name'];
                        break;
                    case 'district':
                        $dataAdress['region'] = $component['name'];
                        break;
                    case 'area'://if not tashkent area will exist 
                        $dataAdress['area'] = $component['name'];
                        break;
                    case 'street':
                        $dataAdress['street'] = $component['name'];
                        break;
                    case 'house':
                        $dataAdress['house'] = $component['name'];
                        break;
                    case 'other':
                        $dataAdress['other'] = $component['name'];
                        break;    
                    default:
                        # code...
                        break;
                }
            }
        }
        
        $this->country_id = 1;
        if(isset($dataAdress['city']) && (isset($dataAdress['region']) || isset($dataAdress['area']) )){
            $city = City::where('name_ru',$dataAdress['city'] )->first();
            if(!$city){
                $city  = new City();
                $city->country_id =  1;
                $city->name_uz =  '';
                $city->name_ru =  $dataAdress['city'];
                $city->save(); 
            }
            $this->city_id = $city->id;
            
            $region = Region::where('name_ru',isset($dataAdress['region'])?$dataAdress['region'] : $dataAdress['area'] )->first();
        
            if(!$region){
                $region  = new Region();
                $region->city_id = $this->city_id;
                $region->name_uz =  '';
                $region->name_ru = isset($dataAdress['region'])?$dataAdress['region'] : $dataAdress['area'];
                $region->save();
            }
            $this->region_id = $region->id;
        }
        $this->addressType = $data['addressType'];
        $this->team_id = $data['team_id'];
        $string ='';
        if(isset($dataAdress['street'])){
            $string.=$dataAdress['street'];
        }
        if(isset($dataAdress['house'])){
            $string.=', '.$dataAdress['house'];
        }
        if(!isset($dataAdress['street']) && isset($dataAdress['other'])){
            $string.=$dataAdress['other'];
        }elseif(isset($dataAdress['other'])){
            $string.=', '.$dataAdress['other'];
        }
        $this->addressline1 = $string;
        $this->longitude = $data['longitude'];
        $this->latitude = $data['latitude'];
        $this->save();
        $companyAdressHistory = new TeamAddressHistory();
        $companyAdressHistory->saveModel($this, $data,'created');
    }
    public function saveModelByIds($id, $data)
    {
        $this->country_id = 1;
        $this->company_id = $id;
        $this->addressType = $data['addressType'];
        $this->city_id = $data['city_id'];
        $this->region_id = $data['region_id'];
        if(isset($data['longitude']) && isset($data['latitude'])){
            $this->longitude = $data['longitude'];
            $this->latitude = $data['latitude'];
        }
        $this->addressline1 =$data['street'].', '.$data['house'];
        $this->save();
        $companyAdressHistory = new TeamAddressHistory();
        $companyAdressHistory->saveModel($this, $data,'created');
    }
    public function updateModelAdress($data)
    {
        $this->city_id = $data['city_id'];
        $this->region_id = $data['region_id'];
        $this->addressline1 =$data['street'].', '.$data['house'];
        $this->save();
        $companyAdressHistory = new TeamAddressHistory();
        $companyAdressHistory->saveModel($this, $data,'updated');
    }
    public function saveModel($data,$status, $team_id)
    {
        $this->country_id = 1;
        $this->team_id = $team_id;
        $this->addressType = $data['addressType'];
        $this->city_id = $data['city_id'];
        $this->region_id = $data['region_id'];
        if(isset($data['longitude']) && isset($data['latitude'])){
            $this->longitude = $data['longitude'];
            $this->latitude = $data['latitude'];
        }
        $this->addressline =$data['addressline'];
        $this->save();
        $companyAdressHistory = new TeamAddressHistory();
        $companyAdressHistory->saveModel($this, $data,$status);
    }
    public function updateModel($data)
    {
        $this->city_id = $data['city_id'];
        $this->region_id = $data['region_id'];
        $this->addressline = $data['addressline'];
        $this->longitude = $data['longitude'];
        $this->latitude = $data['latitude'];
        $this->save();
        $companyAdressHistory = new TeamAddressHistory();
        $companyAdressHistory->saveModel($this, $data,'updated');
    }
    public function deleteModel($data)
    {
        $companyAdressHistory = new TeamAddressHistory();
        $companyAdressHistory->saveModel($this, $data,'deleted');
        $this->delete();
    }
}
