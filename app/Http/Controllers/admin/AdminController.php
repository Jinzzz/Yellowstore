<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Mst_store;
use App\Models\admin\Country;
use App\Models\admin\State;
use App\Models\admin\District;
use App\Models\admin\Town;
use App\Models\admin\Mst_store_documents;
use App\Models\admin\Mst_store_images;
use App\Models\admin\Mst_store_agencies;
use App\Models\admin\Mst_store_link_agency;
use App\Models\admin\Mst_store_companies;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_attribute_value;
use App\Models\admin\Mst_product_image;
use App\Models\admin\Trn_store_customer_otp_verify;
use App\Models\admin\Mst_delivery_boy;
use App\Models\admin\Sys_delivery_boy_availability;
use App\Models\admin\Mst_store_link_delivery_boy;
use App\Models\admin\Trn_delivery_boy_order;
use App\Models\admin\Sys_vehicle_type;
use App\Models\admin\Sys_store_order_status;
use App\Models\admin\Trn_store_order_item;
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_customer_reward_transaction_type;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_store_payment;
use App\Models\admin\Mst_store_link_subadmin;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Sys_payment_type;
use App\Models\admin\Trn_store_payment_settlment;
use App\Models\admin\Trn_delivery_boy_payment_settlment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Image;
use Hash;
use DB;
use Carbon\Carbon;
use Crypt;

use App\Models\admin\Mst_Subadmin_Detail;
use App\Models\admin\Trn_delivery_boy_payment;
use App\Models\admin\Trn_store_payments_tracker;
use App\Models\admin\Trn_sub_admin_payment_settlment;
use App\Models\admin\Trn_subadmin_payments_tracker;
use App\Models\admin\Trn_configure_points;
use App\Models\admin\Trn_registration_point;
use App\Models\admin\Trn_first_order_point;
use App\Models\admin\Trn_referal_point;
use App\Models\admin\Trn_points_to_rupee;
use App\Models\admin\Trn_points_redeemed;
use App\Models\admin\Mst_Tax;
use App\Models\admin\Mst_StoreAppBanner;
use App\Models\admin\Mst_CustomerAppBanner;
use App\Models\admin\Mst_Issues;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listDistricts(Request $request)
	{

	$pageTitle = "Districts";
    $districts = District::orderBy('district_id','DESC')->get();
    $countries   = Country::all();

    	return view('admin.masters.district.list',compact('districts','pageTitle','countries'));

    }

    public function createDistricts(Request $request,District $district)
	{
        $validator = Validator::make($request->all(),
		[
			'state_id' => 'required',
			'district_name'		=> 'required|unique:mst_districts,district_name',

         ],
		[
		    'state_id.required'         => 'State  required',
			'district_name.required'        => 'District  required',
			'district_name.unique'        => 'District name exists',
		]);
                    if(!$validator->fails())
                    {
                    $data= $request->except('_token');
                    $district->state_id 		= $request->state_id;
                    $district->district_name 		= $request->district_name;
                    $district->save();
                    return redirect('admin/districts/list')->with('status','District added successfully.');
                }else
                {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
    }

    public function removeDistricts(Request $request,$district_id ,District $district)
	{
        $district = District::find($district_id);
	 	$delete = $district->delete();

		return redirect('admin/districts/list')->with('status','District deleted successfully');
	}



    public function listTown(Request $request)
	{
        $pageTitle = "Towns";
        $towns = Town::orderBy('town_id','DESC')->get();
       // $districts = District::orderBy('district_name','ASC')->get();
        $countries   = Country::all();
        return view('admin.masters.towns.list',compact('countries','pageTitle','towns'));
    }
    public function removeTown(Request $request,$town_id ,Town $town)
	{
        $town = Town::find($town_id);
	 	$delete = $town->delete();

		return redirect('admin/towns/list')->with('status','Town deleted successfully');
	}

        public function editTown(Request $request,$town_id ,Town $town)
        {
            $town = Town::find($town_id);
            $town->town_name = $request->town_name;
            $town->district_id = $request->district_id;
            $town->update();

            return redirect('admin/towns/list')->with('status','Town updated successfully');
        }

        public function editTownView(Request $request,$town_id)
        {
            $pageTitle = "Edit Town";
            $town = Town::where('town_id',$town_id)->first();
            $d_data = District::where('district_id',$town->district_id)->first();
            $s_data = State::where('state_id',$d_data->state_id)->first();
            $c_data = Country::where('country_id',$s_data->country_id)->first();

            $countries   = Country::all();
            $states = State::where('country_id',$c_data->country_id)->get();
            $districts = District::where('district_id',$town->district_id)->get();


            return view('admin.masters.towns.edit',compact('town','s_data','c_data','town_id','districts','states','pageTitle','countries'));

        }


        public function editDistrictsView(Request $request,$district_id )
        {

            $pageTitle = "Edit District";

            $district = District::where('district_id',$district_id)->first();
            $countries   = Country::all();
            $c_id = State::where('state_id',$district->state_id)->first();
            $states = State::where('country_id',$c_id->country_id)->get();

            return view('admin.masters.district.edit',compact('states','district_id','district','pageTitle','countries'));
        }


        public function editDistricts(Request $request,$district_id ,District $district)
        {
            $district = District::find($district_id);
            $district->district_name = $request->district_name;
            $district->state_id = $request->state_id;
                $district->update();

                return redirect('admin/districts/list')->with('status','District updated successfully');
            }

    public function createTown(Request $request,Town $town)
	{
        $validator = Validator::make($request->all(),
		[
			'district_id' => 'required',
			'town_name'		=> 'required|unique:mst_towns,town_name',
         ],
		[
		    'district_id.required'         => 'District required',
			'town_name.required'        => 'Town  required',
			'town_name.unique'        => 'Town name exists',

		]);
                    if(!$validator->fails())
                    {
                    $data= $request->except('_token');
                    $town->district_id 		= $request->district_id;
                    $town->town_name 		= $request->town_name;
                    $town->save();
                    return redirect('admin/towns/list')->with('status','Town added successfully.');
                }else
                {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
    }


    public function setDefaultImage(Request $request)
    {

        $table_id = $request->table_id; //store photos tabel id
        $store_image = \DB::table('mst_store_images')->select('store_id')->where('store_image_id',$table_id)->first();
        // return response()->json($store_image);
        $store_id = $store_image->store_id;
        // echo $store_image;die;
          $store_images = Mst_store_images::where('store_id',$store_id)->where('default_image',1)->count();
                if($store_images == 0)
                {
                    $affected = DB::table('mst_store_images')
                        ->where('store_image_id', $table_id)
                        ->update(['default_image' => 1]);
                        return true;
                }
                else
                {
                    $affected = DB::table('mst_store_images')
                    ->where('store_id', $store_id)
                    ->update(['default_image' => 0]);

                    $affected = DB::table('mst_store_images')
                    ->where('store_image_id', $table_id)
                    ->update(['default_image' => 1]);
                    return true;
                }
    }


    public function changeDefaultImage(Request $request)
    {

        $table_id = $request->table_id; //store photos tabel id


                    $affected = DB::table('mst_store_images')
                    ->where('store_image_id', $table_id)
                    ->update(['default_image' => 0]);
                    return true;
    }


    public function listVehicleTypes(Request $request)
	{
        $pageTitle = "List Vehicle Types";
        $vehicle_types = Sys_vehicle_type::all();

        return view('admin.masters.vehicle_types.list',compact('pageTitle','vehicle_types'));
    }

    public function listTaxes(Request $request)
	{
        $pageTitle = "List Taxes";
        $taxes = Mst_Tax::all();
        return view('admin.masters.taxes.list',compact('pageTitle','taxes'));
    }

    public function createVehicleTypes(Request $request,Sys_vehicle_type $vehicle_type)
	{

        $vehicle_type->vehicle_type_name  = $request->vehicle_type_name;
      //  dd($vehicle_type);
        $vehicle_type->save();

        return redirect()->back()->with('status','Vehicle type added successfully.');
    }

    public function removeVehicleTypes(Request $request,Sys_vehicle_type $vehicle_type, $vehicle_type_id)
	{
        $vehicle_type = Sys_vehicle_type::find($vehicle_type_id);
        $vehicle_type->delete();

        return redirect()->back()->with('status','Vehicle type removed successfully.');
    }

    public function updateVehicleTypes(Request $request,Sys_vehicle_type $vehicle_type, $vehicle_type_id)
	{
        $vehicle_type = Sys_vehicle_type::find($vehicle_type_id);
        $vehicle_type->vehicle_type_name  = $request->vehicle_type_name;
        $vehicle_type->update();

        return redirect()->back()->with('status','Vehicle type updated successfully.');
    }

    public function createTax(Request $request,Mst_Tax $tax)
	{

        $tax->tax_value  = $request->tax_value;
        $tax->tax_name  = $request->tax_name;
      //  dd($vehicle_type);
        $tax->save();

        return redirect()->back()->with('status','Tax added successfully.');
    }


      public function removeTax(Request $request,Mst_Tax $tax, $tax_id)
	{
        $tax = Mst_Tax::find($tax_id);
        $tax->delete();

        return redirect()->back()->with('status','Tax removed successfully.');
    }

    public function updateTax(Request $request,Mst_Tax $tax, $tax_id)
	{
        $tax = Mst_Tax::find($tax_id);
        $tax->tax_value  = $request->tax_value;
        $tax->tax_name  = $request->tax_name;
        $tax->update();

        return redirect()->back()->with('status','Tax updated successfully.');
    }




    public function listStoreAppBanner(Request $request)
	{
        $pageTitle = "Store App Banners";
        $banners = Mst_StoreAppBanner::all();
        $countries   = Country::all();

        return view('admin.masters.banners.store_app_banners.list',compact('countries','pageTitle','banners'));
    }

    public function storeStoreAppBanner(Request $request,Mst_StoreAppBanner $banner)
	{
        if ($request->hasFile('images')) {

            $img_validate = Validator::make($request->all(),
            [
                'images.*' => 'required|dimensions:width=620,height=290',
                'town_id' => 'required',
            ],
            [
                'images.*.dimensions' => 'Banner image dimensions invalid',
                'town_id.required' => 'Town required',
                ]);
                if($img_validate->fails())
                {
                return redirect()->back()->withErrors($img_validate)->withInput();

                }

        }

         if(!$img_validate->fails())
		{

            if($request->hasFile('images'))
            {



                $images = $request->file('images');
                $town_id = $request->town_id;
               // dd($product_image);
                foreach($images as $image)
                {
                    $filename = time().'.'.$image->getClientOriginalExtension();
                   // dd($filename);
                    $destination_path = 'assets/uploads/store_banner/';

                    $store_img = Image::make($image->getRealPath());
                    $store_img->save($destination_path.'/'.$filename,80);



                        $data2= [[
                            'image'      => $filename,
                            'town_id'      => $town_id,
                                ],
                              ];

                              Mst_StoreAppBanner::insert($data2);

                }
            }
        }

        return redirect()->back()->with('status','Banner image added successfully.');

    }


    public function removeStoreAppBanner(Request $request,Mst_StoreAppBanner $banner,$banner_id)
	{
        $banner = Mst_StoreAppBanner::find($banner_id);
        $banner->delete();
        return redirect()->back()->with('status','Banner image deleted successfully.');

    }

    public function listCustomerAppBanner(Mst_CustomerAppBanner $banner)
	{
        $pageTitle = "Customer App Banners";
        $banners = Mst_CustomerAppBanner::all();
        $countries   = Country::all();

        return view('admin.masters.banners.customer_app_banners.list',compact('countries','pageTitle','banners'));
    }

    public function removeCustomerAppBanner(Request $request,Mst_CustomerAppBanner $banner,$banner_id)
	{
        $banner = Mst_CustomerAppBanner::find($banner_id);
        $banner->delete();
        return redirect()->back()->with('status','Banner image deleted successfully.');

    }

     public function storeCustomerAppBanner(Request $request,Mst_CustomerAppBanner $banner)
	{
        if ($request->hasFile('images')) {

            $img_validate = Validator::make($request->all(),
            [
                'images.*' => 'required|dimensions:min_width=1000,min_height=800',
            ],
            [
                'images.*.dimensions' => 'Banner image dimensions invalid',
                ]);
                if($img_validate->fails())
                {
                return redirect()->back()->withErrors($img_validate)->withInput();

                }

        }

         if(!$img_validate->fails())
		{

            if($request->hasFile('images'))
            {



                $images = $request->file('images');
                $town_id = $request->town_id;
                // dd($product_image);
                foreach($images as $image)
                {
                    $filename = time().'.'.$image->getClientOriginalExtension();
                   // dd($filename);
                    $destination_path = 'assets/uploads/customer_banner/';

                    $store_img = Image::make($image->getRealPath());
                    $store_img->save($destination_path.'/'.$filename,80);



                        $data2= [[
                            'image'      => $filename,
                            'town_id'      => $town_id,
                        ],
                              ];

                              Mst_CustomerAppBanner::insert($data2);

                }
            }
        }

        return redirect()->back()->with('status','Banner image added successfully.');

    }

    public function listIssues()
	{
        $pageTitle = "Issues";
        $issues = Mst_Issues::all();

        return view('admin.masters.issues.list',compact('issues','pageTitle'));
    }

    public function createIssue(Request $request,Mst_Issues $issue)
	{
        $issue->issue  = $request->issue;
        $issue->save();
        return redirect()->back()->with('status','Issue added successfully.');
    }

    public function removeIssue(Request $request,Mst_Issues $issue, $issue_id)
	{
        $issue = Mst_Issues::find($issue_id);
        $issue->delete();
        return redirect()->back()->with('status','Issue removed successfully.');
    }


    public function updateIssue(Request $request,Mst_Issues $issue, $issue_id)
	{
        $issue = Mst_Issues::find($issue_id);
        $issue->issue  = $request->issue;
        $issue->update();
        return redirect()->back()->with('status','Issue updated successfully.');
    }




}
