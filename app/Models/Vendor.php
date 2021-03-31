<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Vendor extends Model
{
    use Notifiable;
    protected $table = 'vendors'; // if this name such as model and adding only s is not must to write it
    protected $fillable = [
        'name', 'mobile','password','address','email','logo','category_id','active','created_at','updated_at'
    ];

    protected $hidden = ['category_id','password'];

    public function scopeActive($query){
        return $query -> where('active',1);
    }

    public function getLogoAttribute($val){
        // asset make for you the link before the masar of the photo such as localhost/ecommerce or www.facebook and so on
        return ($val !== null) ? asset('assets/'.$val) : "";
    }

    public function scopeSelection($query){
        return $query -> select('id','category_id','active','name','address','email','logo','mobile');
    }

    public function category(){
        return $this -> belongsTo('App\Models\MainCategory','category_id','id');
    }

    public function getActive(){
        return $this -> active == 1 ? 'مفعل' : 'غير مفعل';
    }

    public function setPasswordAttribute($password){
        if(!empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }
}
