<?php

namespace App\Helpers;
use Illuminate\Support\Str;
use Crypt;
use  Carbon\Carbon;
use Validator;


class Helper {


    public static function validateStore($valid)
    {
        $validate = Validator::make($valid, [
                'store_name'                       => 'required|unique:mst_stores',
                'store_contact_person_name'        => 'required',
                'store_contact_person_phone_number'=> 'required',
                'store_contact_address'             => 'required',
                'store_country_id'                  => 'required|numeric',
                'store_state_id'                    => 'required|numeric',
                'store_district_id'                  => 'required|numeric',
                'store_town_id'                           => 'required|numeric',
                'store_place'                             => 'required',
                'business_type_id'                  => 'required|numeric',
                'store_username'                  => 'required',
                'store_mobile'                     => 'required|unique:mst_stores|numeric',
                'password'                         => 'required|min:5|same:password_confirmation',


            ],
            [
                'store_name.unique'                => 'Store Name Exists',
                'store_name.required'                => 'Store Name Field required',
                'store_contact_person_name' => 'Contact Person Name Field required',
                'store_contact_address' => 'Address Field required',
                'store_country_id' => 'Country Field is  required',
                'store_state_id' => 'State Field is  required',
                'store_district_id' => 'District Field is  required',
                'store_town_id' => 'Town Field is  required',
                'store_place' => 'Place Field is  required',
                'business_type_id' => 'Buisness Type Field is  required',
                'store_contact_person_phone_number.required' => ' Mobile required',
                'store_username' => 'Store Username is required',
                'password.required'                  => 'Store password required',
                'store_mobile.required'                  => 'Store mobile number required',
                'store_mobile.unique'                  => 'Store mobile number already exists ',

            ]);
        return $validate;
    }




    
}
