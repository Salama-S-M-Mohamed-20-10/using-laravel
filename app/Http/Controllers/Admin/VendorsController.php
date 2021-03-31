<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\MainCategory;
use App\Models\Vendor;
use App\Notifications\VendorCreated;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use DB;
use Illuminate\Support\Str;

class VendorsController extends Controller
{
    public function index(){
       $vendors = Vendor::selection()-> paginate(PAGINATION_COUNT);
       return view('admin.vendors.index',compact('vendors'));
    }
    public function create(){
       $categories = MainCategory::where('translate_of',0)->active()->get();
       return view('admin.vendors.create',compact('categories'));
    }
    public function store(VendorRequest $request){
        try{

            if (!$request->has('active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);

            // save image
            $filepath = "";
            if($request->has('logo')){
                $filepath = uploadImage('vendors',$request->logo);
            }
        //make validation
        //insert to DB
        $vendor = Vendor::create([
            'name' => $request -> name,
            'mobile' => $request -> mobile,
            'email' => $request -> email,
            'active' => $request -> active,
            'address' => $request -> address,
            'logo' => $filepath,
            'password' => $request -> password,
            'category_id' => $request -> category_id
        ]);

        Notification::send($vendor, new VendorCreated($vendor));

        return redirect()->route('admin.vendors')->with(['success' => 'تم الحفظ بنجاح']);
        //redirect message
        }catch(\Exception $ex){
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطأ ما برجاء المحاوله لاحقا']);
        }
    }
    public function edit($id){
        try{
            $vendor = Vendor::Selection()->find($id);
            if(!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا']);

            $categories = MainCategory::where('translate_of',0)->active()->get();

            return view('admin.vendors.edit',compact('vendor','categories'));
        }catch(\Exception $exception) {
                return redirect()->route('admin.vendors')->with(['error' => 'حدث خطأ ما برجاء المحاوله لاحقا']);
        }
    }
    public function update($id,VendorRequest $request){
        try{
        $vendor = Vendor::Selection()->find($id);
        if(!$vendor)
            return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا']);

        DB::beginTransaction();
        // photo
        if($request->has('logo')){
            $filepath = uploadImage('vendors',$request->logo);
            Vendor::where('id',$id)
            -> update([
                'logo' => $filepath,
            ]);
        }

        // password
        $data = $request -> except('_token','id','photo','password');
        if($request->has('password')){
            $data['password'] = $request -> password;
        }

        Vendor::where('id',$id)
            -> update($data); // such as update(['name => $request -> name, mobile => request -> mobile' and so on but he use $data = $request -> except('_token','id','photo','password');])
            DB::commit();
            return redirect()->route('admin.vendors')->with(['success' => 'تم التحديث بنجاح']);
        }catch (\Exception $exception){
              DB::rollback();
              return redirect()->route('admin.vendors')->with(['error' => 'حدث خطأ ما برجاء المحاوله لاحقا']);
        }
    }

    public function destroy($id){
        try {
            $vendor = Vendor::find($id);
            if (!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود ']);

            $image = Str::after($vendor->logo, 'assets/');
            $image = base_path('assets/' . $image);
            unlink($image); //delete from folder

            $vendor->delete();
            return redirect()->route('admin.vendors')->with(['success' => 'تم حذف المتجر بنجاح']);

        } catch (\Exception $ex) {
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function changeStatus($id){
        try{
            $vendor = Vendor::find($id);
            if (!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود ']);

            $status = $vendor -> active == 0 ? 1 : 0;

            $vendor -> update(['active' => $status]);
            return redirect()->route('admin.vendors')->with(['success' => 'تم تغيير الحالة بنجاح']);
        }catch(\Exception $ex){
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}
