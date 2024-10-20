<?php

namespace App\Models\Quizzes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionVariant extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $cast = [
        'question_id'=>'integer',
        'rightAnswer'=>'boolean',
    ];
    
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function saveModel($variant_each, $right_answer,$question_id, $variant_keywords, $data)
    {
        $this->question_id  =$question_id;
        $this->variantText = json_encode($variant_each);
        $this->rightAnswer = $right_answer;
        $this->keyWords =json_encode($variant_keywords);
        $this->save();
        $questionVariantHistory = new QuestionVariantHistory();
        $questionVariantHistory->saveModel($this, $data,'created');
    }
    public function editModel($data)
    {
        
        $this->question_id = $data['question_id'];
        
        $variantText  = json_decode($this->variantText);
        if(isset( $variantText->ru)){
            $test['ru'] = $variantText->ru;
        }
        if(isset( $variantText->uz)){
            $test['uz'] = $variantText->uz;
        }
        if($data['language']=='uz'){
            $test['uz'] = $data['variantText'];
        }else{
            $test['ru'] = $data['variantText'];
        }
        $this->variantText = json_encode($test);
        $this->rightAnswer =  $data['rightAnswer'] ;
        $variant_keywords['ru'] = $data['variantKeywords_ru'];
        $variant_keywords['uz'] = $data['variantKeywords_uz'];
        $this->keyWords =json_encode($variant_keywords);
        $this->save();
        $questionVariantHistory = new QuestionVariantHistory();
        $questionVariantHistory->saveModel($this, $data,'updated');
    }
    public function deleteModel($data)
    {
       
        
            $questionVariantHistory = new QuestionVariantHistory();
            $questionVariantHistory->saveModel($this, $data, 'deactivated');
            $this->delete();
        
    }
}
