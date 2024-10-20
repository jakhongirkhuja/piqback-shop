<?php

namespace App\Models\Phonebook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phonebook extends Model
{
    use HasFactory;
    protected $connection = 'pgsql7';
    public function saveModel($data, $status)
    {
        $this->title= $data['title'];
        $this->filters= $data['filters'];
        $this->save();
        $phonebookhistory = new PhonebookHistory();
        $phonebookhistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data)
    {
        $promoceCodehistory = new PhonebookHistory();
        $promoceCodehistory->saveModel($this,$data,'deleted');
        $this->delete();
    }
}
