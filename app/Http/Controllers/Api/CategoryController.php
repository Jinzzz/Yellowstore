<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Mst_categories;

class CategoryController extends Controller
{
    public function list(Request $request)
    {
        $data = array();
        $cat = array();
        
        try {

            $Category = Mst_categories::select('category_id','category_name')->orderBy('category_id','DESC')->get();
            $catlist = array();
            if ($Category) {
                foreach ($Category as $Categorys) {
                    $catlist['category_id'] = $Categorys->category_id;
                    $catlist['category_name'] = ucFirst($Categorys->category_name);
                    array_push($cat, $catlist);
                }
            }
            $data['status'] = 1;
            $data['message'] = "success";
            return response($cat);
           
           
        }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];

            return response($response);
        }
    }
}
