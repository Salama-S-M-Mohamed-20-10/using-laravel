<?php

namespace App\Models;

use App\Observers\MainCategoryObserver;
use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    protected $table = 'main_categories'; // if this name such as model and adding only s is not must to write it
    protected $fillable = [
        'translation_lang', 'translate_of', 'name','slug','photo','active','created_at','updated_at'
    ];

    protected static function boot()
    {
        parent::boot();
        MainCategory::observe(MainCategoryObserver::class);
    }

    public function scopeActive($query){
        return $query -> where('active',1);
    }

    public function scopeSelection($query){
        return $query -> select('id','translation_lang','name','slug','photo','active','translate_of');
    }
    public function getPhotoAttribute($val){
        // asset make for you the link before the masar of the photo such as localhost/ecommerce or www.facebook and so on
        return ($val !== null) ? asset('assets/'.$val) : "";
    }
    public function getActive(){
        return $this -> active == 1 ? 'مفعل' : 'غير مفعل';
    }
    public function categories(){
        // relation between translation_lang and translate_of
        // $this is that i enter in it in the page of edit
         return $this -> hasMany(self::class,'translate_of');
    }
    public function vendors(){
        return $this -> hasMany('App\Models\Vendor','category_id','id');
    }
}
