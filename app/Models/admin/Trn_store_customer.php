<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class Trn_store_customer extends Authenticatable
{
    use HasApiTokens, Notifiable; 
    
    protected $primaryKey = "customer_id";
    protected $guard =  "customer";
    protected $table = "trn_store_customers";

    protected $fillable = [

                   'customer_id','customer_first_name','customer_last_name','customer_email',
                   'customer_mobile_number',
                   'customer_address','country_id','state_id','customer_location',
                   'customer_pincode','customer_bank_account','customer_username',
                   'customer_password',
                   'customer_profile_status','customer_otp_verify_status',
                   'district_id','town_id','address_2','gender','dob'
                  ];


   public function country()
   {
    return $this->belongsTo('App\Models\admin\Country','country_id','country_id');
   }
   public function state()
   {
    return $this->belongsTo('App\Models\admin\State','state_id','state_id');
   }

   public function district()
   {
    return $this->belongsTo('App\Models\admin\District','district_id','district_id');
   }

   public function town() //town district relation
   {
    return $this->belongsTo('App\Models\admin\Town','town_id','town_id');
   }
   
    public function AauthAcessToken()
    {
      return $this->hasMany('\App\Models\OauthAccessToken','user_id','store_id');
    }

}
