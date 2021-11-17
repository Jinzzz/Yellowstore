<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Mst_store;
use App\Models\admin\Mst_store_documents;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Trn_store_otp_verify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;
use DB;
use Carbon\Carbon;
use Crypt;
use Str;
use Auth;


class StoreController extends Controller
{
    
    public function mobCheck(Request $request){
      $data = array();
        try{
        $storMob=$request->store_mobile;
        if ($storMob) {
        	$storMobCheck =Mst_store::where("store_mobile",'=',$storMob)->first();
	        if($storMobCheck)
	        {
	            $data['status'] = 0;
	            $data['message'] = "Mobile Number Already in use";

	        }else{
	            $data['status'] = 1;
	            $data['message'] = "Mobile Number Accepted";
	        }
        }else{
        	$data['status'] = 2;
	        $data['message'] = "Mobile Number cannot be empty";
        }
        
        return response($data);
       
          }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];
            return response($response);
        }

    }

    
    public function nameCheck(Request $request){
      $data = array();
        try{
        $storName=$request->store_name;
        if ($storName) {
        	$storNameCheck =Mst_store::where("store_name",'=',$storName)->first();
	        if($storNameCheck)
	        {
	            $data['status'] = 0;
	            $data['message'] = "Store Name Already in use";

	        }else{
	            $data['status'] = 1;
	            $data['message'] = "Store Name Accepted";
	        }
        }else{
        	$data['status'] = 2;
	        $data['message'] = "Store Name cannot be empty";
        }
        
        return response($data);
       
          }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];
            return response($response);
        }

    }

    
    public function saveStore(Request $request,Mst_store $store,Mst_store_documents $store_doc,
        Trn_store_otp_verify $otp_verify){
      	$data = array();
        try{

        	$validator = Helper::validateStore($request->all());
        	if(!$validator->fails())
            {
            	$store->store_name            = $request->store_name;
            	$store->store_name_slug       =Str::of($request->store_name)->slug('-');
            	$store->store_contact_person_phone_number = $request->store_contact_person_phone_number;
            	$store->store_mobile   = $request->store_mobile;
	            $store->store_added_by        = 0;
	            $store->password              = Hash::make($request->password);
	            $store->store_account_status       = 0;
	            $store->store_otp_verify_status    = 0;
	            $store->store_contact_person_name            = $request->store_contact_person_name;
	            $store->store_primary_address            = $request->store_contact_address;
	            $store->store_country_id            = $request->store_country_id;
	            $store->store_state_id            = $request->store_state_id;
	            $store->store_district_id            = $request->store_district_id;
	            $store->town_id            = $request->store_town_id;
	            $store->place            = $request->store_place;
	            $store->business_type_id            = $request->business_type_id;
	            $store->store_username            = $request->store_username;
	            $timestamp = time();
	            \QrCode::format('svg')->size(500)->generate($timestamp,'assets/uploads/store_qrcodes/'.$timestamp.'.svg');
	            $store->store_qrcode          = $timestamp;
	            $store->save();
	            	$store_id = DB::getPdo()->lastInsertId();
			            $store_doc->store_id            = $store_id;
			            $store_doc->store_document_gstin            = $request->store_gst_number;
			            $store_doc->save();
			            $store_otp =  rand ( 1000 , 9999 );
			            $store_otp_expirytime = Carbon::now()->addMinute(10);

			            $otp_verify->store_id                 = $store_id;
			            $otp_verify->store_otp_expirytime     = $store_otp_expirytime;
			            $otp_verify->store_otp                 = $store_otp;
			            $otp_verify->save();

			    $data['store_id'] = $store_id;
			    $data['otp'] = $store_otp;
            	$data['status'] = 1;
            	$data['message'] = "Store Registration Success";   

            }else{
            	$data['errors'] = $validator->errors();
            	$data['status'] = 0;
            	$data['message'] = "Store Registration Failed";
            }

        	
       
        
        return response($data);
       
          }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];
            return response($response);
        }

    }

    
    public function loginStore(Request $request)
    {

    	$data = array();
        try
        {
    	   $phone = $request->input('store_mobile');
    	   $passChk = $request->input('password');
    	   // $devType = $request->input('device_type');
        //    $devToken = $request->input('device_token');

           $validator = Validator::make($request->all(), [      
                'store_mobile' => 'required',  
                'password' => 'required',
                // 'device_type' => 'required',
                // 'device_token' => 'required',
            ],
            [   
                'store_mobile.required' => "Store Mobile Number is required", 
                'password.required' => "Password is required",
                // 'device_type.required' => "Device Type is required",
                // 'device_toke.required' => "Device Token is required",
            ]);
            if(!$validator->fails())
                {
    	           $custCheck = Mst_store::where('store_mobile','=',$phone)->first();

    	           if($custCheck)
    	               {
                        
    		              if(Hash::check($passChk, $custCheck->password))
        	                   {
                                    if($custCheck->store_account_status!=0)
                                        {
                                            if($custCheck->store_otp_verify_status!=0)
                                                {  
                                                 if(Auth::guard('store')->attempt(['store_mobile' => request('store_mobile'), 'password' => request('password')])){
                                                    $user = Mst_store::select('mst_stores.*')->find(auth()->guard('store')->user()->store_id);
                                                    // $success =  $user;
                                                    // dd($user);
                                                    $data['token'] =  $user->createToken('authToken',['store'])->accessToken;                                                   
                                                    $data['status'] = 1;
                                                    $data['message'] = "Login Success";
                                                    $data['store_id'] = $custCheck->store_id;
                                                    $data['store_name'] = $custCheck->store_name;
                                                    $data['store_contact_person_name'] = $custCheck->store_contact_person_name;
                                                    $data['store_username'] = $custCheck->store_username;

                                                    // $data['access_token'] =  $store->createToken('authToken')->accessToken;
                                                    // $data['access_token'] = $custCheck->createToken('api-customer')->accessToken;

                                                    }
                                                     // $data['access_token'] = auth()->guard('api')->user()->createToken('authToken')->accessToken;
                                                }else{
                                                    $data['status'] = 2;
                                                    $data['message'] = "OTP not verified";
                                                }
                                        }else{
                                            $data['status'] = 4;
                                            $data['message'] = "Profile not Activated";
                                        }
                    	    	}else{
                    	    		$data['status'] = 3;
                    	    		$data['message'] = "Mobile Number or Password is Invalid";
                    	    	}
                    	}else{
                    		$data['status'] = 0;
                    		$data['message'] = "Invalid Login Details";
                    	}
                }else{
                    $data['errors'] = $validator->errors();
                    $data['message'] = "Login Failed";
                }

    	return response($data);

        }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];
            return response($response);
        }

    }

    
    public function verifyOtp(Request $request){
      $data = array();
        try{
        	$otp = $request->input('store_otp');
            $storeId = $request->input('store_id');
            $otp_verify =  Trn_store_otp_verify::where('store_id', '=', $storeId)->first();
        	if($otp_verify)
           		{
        			$store_otp_exp_time = $otp_verify->store_otp_expirytime;
        			$current_time = Carbon::now()->toDateTimeString();
            		$store_new_otp =  $otp_verify->store_otp;

            		 if($store_new_otp == $request->store_otp)
                		{
			                if($current_time < $store_otp_exp_time)
			                {
                       			$store = Mst_store::Find($store_id);
			                    $store->store_account_status = 1;
			                    $store->store_otp_verify_status = 1;
			                    $store->update();

                      			$data['status'] = 1;
                                $data['message'] = "OTP Verifiction Success";
                      
                    		} else{
                    			$data['status'] = 2;
                                $data['message'] = "OTP expired.click on resend OTP";	
                    		}

                			}else{
                				$data['status'] = 3;
                                $data['message'] = "Incorrect OTP entered. Please enter a valid OTP.";
                			}
            				}else{
            					$data['status'] = 3;
                                $data['message'] = "Store OTP not found. Please click on resend OTP.";
            				}
       
        
        			return response($data);
       
          }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];
            return response($response);
        }

    }




}
