<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'logo' => 'required_without:id|mimes:jpg,jpeg,png',
            'name' => 'required|string|max:100',
            'mobile' => 'required|max:100|unique:vendors,mobile,'.$this -> id,//$this -> id for make it not unique in edit vendor and become unique in create only
            'email' => 'required|email|unique:vendors,email,'.$this -> id, //$this -> id for make it not unique in edit vendor and become unique in create only
            'category_id' => 'required|exists:main_categories,id', // i can do not write id because he such as convenient
            'address' => 'required|string|max:500',
            'password' => 'required_without:id'
        ];
    }

    public function messages(){
        return [
            'required' => 'هذا الحقل مطلوب',
            'max' => 'هذا الحقل طويل',
            'category_id.exists' => 'القسم غير موجود',
            'email.email' => 'صيغة البريد الالكتروني غير صحيحة',
            'address.string' => 'العنوان لابد ان يكون حروف او حروف وارقام',
            'name.string' => 'الاسم لابد ان يكون حروف او حروف و ارقام',
            'logo.required_without' => 'الصورة مطلوبة',
            'email.unique' => 'البريد الالكتروني مستخدم من قبل',
            'mobile.unique' => 'رقم الهاتف مستخدم من قبل'
        ];

    }
}
