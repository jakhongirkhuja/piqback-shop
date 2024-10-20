<?php

namespace App\Models\Wish;

use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $connection = 'pgsql5';
    public function saveModel($user, $data)
    {
        $this->user_id = $user->id;
        $this->course_id = $data['course_id'];
        $this->save();
        $wishlistHistory = new WishlistHistory();
        $wishlistHistory->saveModel($this, $data, 'added');
    }
    public function updateModel($data, $status){
        $wishlistHistory = new WishlistHistory();
        $wishlistHistory->saveModel($this, $data, $status);
        $this->delete();
    }
    /**
     * Get the user that owns the Wishlist
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courselist()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    
}
