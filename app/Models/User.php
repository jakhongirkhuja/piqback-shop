<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'gender',
        'birthDate',
        'language',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    public function phonebook(){
        return $this->hasOne(Phonebook::class);
    }
    public function email(){
        return $this->hasOne(Email::class);
    }
    protected $casts = [
        'email_verified_at' => 'datetime',
        'gender'=>'integer',
        'user_id'=>'string',
        'birthDate'=>'datetime:Y-m-d',
        'hrid'=>'string',
    ];
    public function saveModel($data)
    {
        $this->firstName= $data['firstName'];
        $this->lastName= $data['lastName'];
        $this->gender= $data['gender'];
        $this->birthDate= $data['birthDate'];
        $this->role= $data['role'];
        $this->save();
        $userhistory =new UserBioHistoires();
        $userhistory->saveModel($this, $data);
    }
    public function updateModel($data)
    {
        $this->firstName= $data['firstName'];
        $this->lastName= $data['lastName'];
        $this->gender= $data['gender'];
        $this->birthDate= $data['birthDate'];
        $this->role= $data['role'];
        $this->language = $data['language'];
        $this->save();
        $userhistory =new UserBioHistoires();
        $userhistory->updateModel($this, $data);
    }
    public function updateModelGlobal($data)
    {
        $this->firstName= $data['firstName'];
        $this->lastName= $data['lastName'];
        $this->gender= $data['gender'];
        $this->birthDate= date('Y-m-d', strtotime($data['birthDate']));
        $this->role = $data['role'];
        $this->language = $data['language'];
        $this->save();
        $userhistory =new UserBioHistoires();
        $userhistory->updateModel($this, $data);
    }
    public function saveModelRegister($data)
    {
        $this->firstName= $data['firstName'];
        $this->lastName= $data['lastName'];
        $this->gender= $data['gender'];
        $this->birthDate= date('Y-m-d', strtotime($data['birthDate']));
        $this->role = $data['role'];
        $this->hrid = hrtime(true);
        
        $this->save();
        $userhistory =new UserBioHistoires();
        $userhistory->saveModel($this, $data);
        
    }
    public function updateUserRole($role, $data){
        $this->role = $role;
        $this->save();
       
        $userRoleHistory = new UserRoleHistoires();
       
        $userRoleHistory->saveModel($this,$data, '1');
       
    }
    public function deleteModel($data)
    {
        $userhistory =new UserBioHistoires();
        $userhistory->updateModel($this, $data);
        $this->delete();
    }
    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->hrid = hrtime(true);
        });

    }
}
