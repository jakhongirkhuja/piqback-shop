<?php

namespace App\Models\Phonebook;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhonebookHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql7';
    public function saveModel($model, $data, $status)
    {
        $this->title= $model->title;
        $this->filters= $model->filters;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('phonebook_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql7');
    }
}
