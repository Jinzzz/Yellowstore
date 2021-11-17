<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Town;

class TownController extends Controller
{
    public function townList(Request $request)
    {
        $data = array();
        try {
        	$districtId = $request->district_id;
                if ($districtId) {
                    $data['Town_List'] = Town::where('district_id','=',$districtId)->select('town_id','town_name')->orderBy('town_name','ASC')->get();
                }else{
                            $data['status'] = 0;
                            $data['message'] = "District ID Cannot be Empty.";
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
