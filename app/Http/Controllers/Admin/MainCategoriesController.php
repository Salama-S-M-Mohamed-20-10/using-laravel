<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoryRequest;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;
use Illuminate\Support\Str;

class MainCategoriesController extends Controller
{
    public function index () {

         $default_lang = get_default_lang();
         // we take get because all the category of the admin but first return to me one thing only
         $categories = MainCategory::where('translation_lang',$default_lang) -> selection() -> get();

         return view('admin.maincategories.index',compact('categories'));
    }

    public function create(){
        return view('admin.maincategories.create');
    }

    public function store(MainCategoryRequest $request){
        // validation
        // save db

        try{
        //return $request;
        // i save all categories in $main_categories and use collect to convert array to collection
        $main_categories = collect($request -> category);

        $filter = $main_categories -> filter(function ($value,$key){
             return $value['abbr'] == get_default_lang();
        });

        $default_category = array_values($filter -> all() ) [0];

        $filepath = "";
        if($request->has('photo')){
            $filepath = uploadImage('maincategories',$request->photo);
        }

        DB::beginTransaction();
        // code here transaction DB
        $default_category_id = MainCategory::insertGetId([
            'translation_lang' => $default_category['abbr'],
            'translate_of' => 0,
            'name' => $default_category['name'],
            'slug' => $default_category['name'],
            'photo' => $filepath
        ]);

        $categories = $main_categories -> filter(function ($value,$key){
            return $value['abbr'] != get_default_lang();
       });

       if(isset($categories) && $categories -> count()){
           $categories_arr=[];
           foreach($categories as $category){
            $categories_arr[] = [
                'translation_lang' => $category['abbr'],
                'translate_of' => $default_category_id,
                'name' => $category['name'],
                'slug' => $category['name'],
                'photo' => $filepath
            ];
           }
           // he said you do not able use here create
           MainCategory::insert($categories_arr);
       }
       DB::commit();

       return redirect()->route('admin.maincategories')->with(['success' => 'تم الحفظ بنجاح']);
    }catch(\Exception $ex){
        DB::rollback();
        return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطأ ما برجاء المحاوله لاحقا']);
    }
    }

    public function edit($mainCat_id){
        // get specific categories and its translations
        $mainCategory = MainCategory::with('categories')
        ->selection()
        ->find($mainCat_id);

        if(!$mainCategory)
            return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);

        return view('admin.maincategories.edit',compact('mainCategory'));
    }

    public function update($mainCat_id,MainCategoryRequest $request){

        //return $request;

        try{

        // validation

        // find main id

        $main_category = MainCategory::find($mainCat_id);

        if(!$main_category)
            return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);

        // update data

        $category = array_values($request -> category ) [0];

        if (!$request->has('category.0.active'))
             $request->request->add(['active' => 0]);
        else
             $request->request->add(['active' => 1]);


        MainCategory::where('id',$mainCat_id)
        -> update([

            'name' => $category['name'],
            'active' => $request -> active,

        ]);

        // save image

        $filepath=$main_category -> photo;
        if($request->has('photo')){
            $filepath = uploadImage('maincategories',$request->photo);
            MainCategory::where('id',$mainCat_id)
            -> update([
                'photo' => $filepath,
            ]);
        }



        return redirect()->route('admin.maincategories')->with(['success' => 'تم التحديث بنجاح']);

        }catch(\Exception $ex){
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطأ ما برجاء المحاوله لاحقا']);
        }
    }

    public function destroy($id){
        try {
            $maincategory = MainCategory::find($id);
            if (!$maincategory)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);

            $vendors = $maincategory->vendors();
            if (isset($vendors) && $vendors->count() > 0) {
                return redirect()->route('admin.maincategories')->with(['error' => 'لأ يمكن حذف هذا القسم  ']);
            }

            $image = Str::after($maincategory->photo, 'assets/');
            $image = base_path('assets/' . $image);
            unlink($image); //delete from folder

            //delete translation
            $maincategory -> categories() -> delete();
            $maincategory->delete();
            return redirect()->route('admin.maincategories')->with(['success' => 'تم حذف القسم بنجاح']);

        } catch (\Exception $ex) {
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function changeStatus($id){
        try{
            $maincategory = MainCategory::find($id);
            if (!$maincategory)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);

            $status = $maincategory -> active == 0 ? 1 : 0;

            $maincategory -> update(['active' => $status]);
            return redirect()->route('admin.maincategories')->with(['success' => 'تم تغيير الحالة بنجاح']);
        }catch(\Exception $ex){
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}
