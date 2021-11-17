<?php

namespace App\Http\Controllers\store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\admin\Mst_store;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Sys_store_order_status;
use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_store_agencies;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Mst_attribute_value;
use App\Models\admin\Mst_product_image;
use App\Models\admin\Mst_store_link_agency;
use App\Models\admin\Mst_order_link_delivery_boy;
use App\Models\admin\Country;
use App\Models\admin\State;
use App\Models\admin\District;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_store_order_item;
use App\Models\admin\Mst_delivery_boy;
use App\Models\admin\Trn_order_invoice;

use App\Models\admin\Mst_dispute;
use App\Models\admin\Mst_Tax;
use App\Models\admin\Town;
use App\Models\admin\Trn_store_setting;
use App\Models\admin\Trn_StoreTimeSlot;

use App\Models\admin\Mst_store_documents;
use App\Models\admin\Mst_store_images;
use App\Models\admin\Mst_store_link_delivery_boy;


use App\Models\admin\Trn_StoreAdmin;
use App\Models\admin\Trn_StoreDeliveryTimeSlot;



use App\Models\admin\Trn_store_customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Response;
use Image;
use DB;
use Hash;
use Carbon\Carbon;
use Crypt;
use Mail;
use PDF;



class StoreController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:store');
    }


    public function index()
    {


    	$pageTitle = "Store";

    	$user_id =   Auth::guard('store')->user()->store_id;

    	$store = Mst_store::where('store_id','=',$user_id)->get();
      $product = Mst_store_product::where('store_id','=',$user_id)->get()->count();
      $order = Trn_store_order::where('store_id','=',$user_id)->get()->count();
      $agency = Mst_store_link_agency::where('store_id','=',$user_id)->get()->count();
      $total_sale = Trn_store_order::where('store_id','=',$user_id)->sum('product_total_amount');
      $today_sale = Trn_store_order::where('store_id','=',$user_id)->where('created_at',now()->day)->sum('product_total_amount');
      $today_sale_count = Trn_store_order::where('store_id','=',$user_id)->where('created_at',now()->day)->count();
      $delivery_boys = Mst_delivery_boy::where('store_id','=',$user_id)->count();
      $dispute = \DB::table("mst_disputes")->where('store_id','=',$user_id)->count();
      $dispute_current = \DB::table("mst_disputes")->where('dispute_status','=','open')->where('store_id','=',$user_id)->count();
      $dispute_new = \DB::table("mst_disputes")->where('dispute_status','=','open')->where('store_id','=',$user_id)->where('created_at',now()->day)->count();

    	return view('store.home',compact('dispute_new','dispute_current','dispute','delivery_boys','today_sale_count','today_sale','total_sale','store','pageTitle','product','order','agency'));
    }

    public function changePassword()
    {

        $pageTitle = "Update Password";
        $user_id = Auth::guard('store')->user()->store_id;

        $user = Mst_store::where('store_id','=',$user_id)->first();

        return view('store.elements.password.update_password',compact('pageTitle','user'));
    }

     public function updatePassword(Request $request, Mst_store $store)
    {

            $store_id = Auth::guard('store')->user()->store_id;

            $store = Mst_store::Find($store_id);

            $validator = Validator::make($request->all(),
            [
                'password'         => 'required|same:password_confirmation',

            ],
            [
                'password.required'        => 'Password required',



            ]);
          // $this->uploads($request);
            if(!$validator->fails())
            {
            $data= $request->except('_token');


              if (Hash::check($request->old_password, $store->password)) { 
                $data2= [
                  'password'      => Hash::make($request->password),
                      
                    ];
                Mst_store::where('store_id',$store_id)->update($data2);
    
              
              }
              else
              {
                  return redirect()->back()->with('errstatus','Old password incorrect.');

              }
              return redirect()->back()->with('status','Password updated successfully.');

        }else
        {

            return redirect()->back()->withErrors($validator)->withInput();
        }


  }

    public function Profile()
    {

        $pageTitle = "Update Profile";
        $store_id = Auth::guard('store')->user()->store_id;


        $store = Mst_store::where('store_id', '=', $store_id)->first();
        $countries = Country::all();
        $store_documents  = Mst_store_documents::where('store_id','=',$store_id)->get();
        $store_images = Mst_store_images::where('store_id','=',$store_id)->get();
        $agencies = Mst_store_link_agency::where('store_id','=',$store_id)->get();
    
            $delivery_boys = Mst_store_link_delivery_boy::where('store_id','=',$store_id)->get();
    
           
                $all_delivery_boys = \DB::table('mst_delivery_boys')
                ->join('mst_stores','mst_stores.store_id','=','mst_delivery_boys.store_id')
                ->where('mst_stores.subadmin_id',$store->subadmin_id)
                ->get();
           
    
            $delivery_boys = Mst_store_link_delivery_boy::where('store_id','=',$store_id)->get();
        $business_types = Mst_business_types::where('business_type_status','=',1)->get();
    




        // $store = Mst_store::where('store_id','=',$user_id)->first();
        // $countries = Country::all();
        return view('store.elements.update_profile',compact('all_delivery_boys','store','pageTitle',
        'countries','store_images','store_documents','agencies','delivery_boys','business_types'));
    }





    public function updateProfile(Request $request, Mst_store $store)
    {

    	 $store_Id = $request->store_id;
    	 $store = Mst_store::Find($store_Id);

       $store_id = Auth::guard('store')->user()->store_id;
    	 


    	$validator = Validator::make($request->all(),
		[
		    'store_name'    => 'required|unique:mst_stores,store_name,'.$store_id.',store_id',
			'store_contact_person_name'        => 'required',
			'store_contact_person_phone_number'=> 'required',
			'store_pincode'				       => 'required',
			'store_primary_address'            => 'required',
			'store_country_id'			       => 'required',
			'store_state_id'       		       => 'required',
			//'email'       		       => 'required',


			//'store_commision_amount'			       => 'required',

			'store_district_id'                => 'required',
			'store_username'   => 'required|unique:mst_stores,store_username,'.$store_id.',store_id',
			//'store_commision_percentage' =>'required',


         ],
		[
		    'store_name.required'         				 => 'Store name required',
			'store_contact_person_name.required'     	 => 'Contact person name required',
			'store_contact_person_phone_number.required' => 'Contact person number required',

              //  'email.required'         				 => 'Email required',

			'store_pincode.required'        			 => 'Pincode required',
			'store_primary_address.required'             => 'Primary address required',
			'store_country_id.required'         		 => 'Country required',
			'store_state_id.required'        			 => 'State required',
			'store_district_id.required'        		 => 'District  required',
			'store_username.required'        			 => 'Username required',
			//'store_commision_amount.required'                => 'Store commision amount required',

			//'store_commision_percentage.required'	=>'Store commision percentage required',



		]);


		     if ($request->hasFile('store_document_other_file')) {

                $doc_validate = Validator::make($request->all(),
                [
                    'store_document_other_file.*'        => 'mimes:pdf,doc,docx,txt',
                ],
                [
                    'store_document_other_file.*.mimes' => "store documents file format error",
                    ]);
                    if($doc_validate->fails())
                    {
		            return redirect()->back()->withErrors($doc_validate)->withInput();

                    }

            }

            if ($request->hasFile('store_image')) {

                $img_validate = Validator::make($request->all(),
                [
                    'store_image.*' => 'required|dimensions:min_width=1000,min_height=800',
                ],
                [
                    'store_image.*.dimensions' => 'store image dimensions invalid',
                    ]);
                    if($img_validate->fails())
                    {
		            return redirect()->back()->withErrors($img_validate)->withInput();

                    }

            }

        if(!$validator->fails())
		{
     	$data= $request->except('_token');


       $data= [

        'store_name' =>  $request->store_name,
        'store_name_slug' => Str::of($request->store_name)->slug('-'),
        'store_contact_person_name' => $request->store_contact_person_name,
        'store_mobile' => $request->store_mobile,
        'store_contact_person_phone_number' => $request->store_contact_person_phone_number,
        'store_website_link' => $request->store_website_link,
        'store_pincode' => $request->store_pincode,
        'store_primary_address' => $request->store_primary_address,
        'email' => $request->email,
        'store_country_id' => $request->store_country_id,
        'store_state_id' => $request->store_state_id,
        'store_district_id' => $request->store_district_id,
        'business_type_id' => $request->business_type_id,
        'store_username' => $request->store_username,
        'store_commision_percentage' => $request->store_commision_percentage,
        'store_commision_amount' => $request->store_commision_amount,
        'town_id' => $request->store_town,
        'place' => $request->store_place,

       ];

       Mst_store::where('store_id',$store_id)->update($data);
	

		$date = Carbon::now();
 		if ($request->hasFile('store_document_other_file')) {



            $allowedfileExtension = ['pdf', 'doc', 'txt',];
                  $files = $request->file('store_document_other_file');
                  $files_head = $request->store_document_other_file_head;
                    $k = 0;
                  foreach ($files as $file) {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();

                    $file->move('assets/uploads/store_document/files', $filename);

                        $data1= [[
                                'store_id'               => $store_id,
                            'store_document_license'  => $request->store_document_license,
                            'store_document_gstin'     => $request->store_document_gstin,
                            'store_document_file_head' => $files_head[$k],
                            'store_document_other_file' => $filename,
                            'created_at'         		=> $date,
                            'updated_at'         		=> $date,
                                    ],
                                  ];

                             Mst_store_documents::insert($data1);
                             $k++;

                }
              }


		// multiple image upload

		        if($request->hasFile('store_image'))
                {



                    $store_image = $request->file('store_image');
                   // dd($product_image);
                    foreach($store_image as $image)
                    {
                        $filename = time().'.'.$image->getClientOriginalExtension();
                       // dd($filename);
                        $destination_path = 'assets/uploads/store_images/images';

                        $store_img = Image::make($image->getRealPath());
                        $store_img->save($destination_path.'/'.$filename,80);



                            $data2= [[
                                'store_image'      => $filename,
                                'store_id' 			=> $store_id,
                                'created_at'         => $date,
                                'updated_at'         => $date,
                                    ],
                                  ];

                             Mst_store_images::insert($data2);

                    }
                }

        return redirect('store/home')->with('status','Profile updated successfully.');
	}else
	{

		return redirect()->back()->withErrors($validator)->withInput();
	}



}

  public function destroyStore_Doc(Request $request,Mst_store_documents $document)
    {


      $document =  $document->delete();

      return redirect()->back()->with('status','Document deleted successfully');
    }

    public function destroyStore_Image(Request $request,Mst_store_images $image)
    {
  
      $image = $image->delete();
  
      return redirect()->back()->with('status','Image deleted Successfully');;
    }
  

     public function listProduct(Request $request)
    {



      try {
        

        $pageTitle = "Products";
        $store_id =  Auth::guard('store')->user()->store_id;
        $products = Mst_store_product::where('store_id',$store_id)->orderBy('product_id', 'DESC')->get();
        //dd($products);
        $store = Mst_store::all();

      if($_GET){

    //    echo "here";die;


        $product_name = $request->product_name;
        $product_code = $request->product_code;
        $stock_status =  $request->stock_status;
        $product_status =  $request->product_status;

        $a1 = Carbon:: parse($request->From_date)->startOfDay();
        $a2 = Carbon:: parse($request->To_date)->endOfDay();
        $b1 = $request->start_price;
        $b2 = $request->end_price;

        // $a[] = Carbon:: parse($request->From_date)->startOfDay();
        // $a[] = Carbon:: parse($request->To_date)->endOfDay();
        // $b[] = $request->start_price;
        // $b[] = $request->end_price;

        // $products = Mst_store_product::where('product_name','like', '%'.$product_name.'%')
        //     ->where('product_code','like', '%'.$product_code.'%')
        //     ->where('stock_status','like', '%'.$stock_status.'%')
        //     ->where('product_status','like', '%'.$product_status.'%')
        //     ->where('store_id','like', '%'.$store_id.'%')
        //     ->whereBetween('created_at',[$a,$a])
        //      ->whereBetween('product_price',[$b,$b])
        //     ->get();

            DB::enableQueryLog();
       // print( Auth::guard('store')->user()->store_id);die;

    	$store_id =   Auth::guard('store')->user()->store_id;


         $query =  Mst_store_product::where('store_id',$store_id);

            if(isset($product_name))
            {
                $query = $query->where('product_name','like', '%'.$product_name.'%');
            }

            if(isset($request->From_date) && isset($request->To_date))
            {
                $query = $query->whereBetween('created_at',[$a1,$a2]);
            }

            if(isset($request->start_price) && isset($request->end_price))
            {
                $query = $query->whereBetween('product_price_offer',[$b1,$b2]);
            }

            if(isset($request->start_price) && !isset($request->end_price))
            {
                $query = $query->where('product_price_offer','>=',$b1);
            }

            if(!isset($request->start_price) && isset($request->end_price))
            {
                $query = $query->where('product_price_offer','<=',$b2);
            }

            if(isset($product_code))
            {
                $query = $query->where('product_code','like', '%'.$product_code.'%');
            }

            if(isset($stock_status))
            {
                $query = $query->where('stock_status',$stock_status);
            }

            if(isset($product_status))
            {
                $query = $query->where('product_status',$product_status);
            }


            $products = $query->get();
//dd(DB::getQueryLog());

              return view('store.elements.product.list',compact('products','pageTitle','store'));
            }

        return view('store.elements.product.list',compact('products','pageTitle','store'));

      } catch (\Exception $e) {
      
        return redirect()->back()->withErrors(['Something went wrong!'])->withInput();

    }
 }


public function createProduct()
{
     $pageTitle = "Create Products";

    $products = Mst_store_product::all();
    $attr_groups = Mst_attribute_group::all();
    $tax = Mst_Tax::all();

    $colors = Mst_attribute_value::join('mst_attribute_groups','mst_attribute_groups.attr_group_id','=','mst_attribute_values.attribute_group_id')
    ->where('mst_attribute_groups.group_name','LIKE','%color%')
    ->select('mst_attribute_values.*')
    ->get();
    $agencies = Mst_store_agencies::all();


    $business_types = Mst_business_types::all();
    $store = Mst_store::all();

    return view('store.elements.product.create',compact('agencies','colors','tax','products','pageTitle','attr_groups','store','business_types'));
}

public function GetAttr_Value(Request $request)
    {
        $grp_id = $request->attr_group_id;
       // dd($grp_id);
        $attr_values  = Mst_attribute_value::where("attribute_group_id",'=',$grp_id)
        ->pluck("group_value","attr_value_id");


        return response()->json($attr_values);

    }
 public function GetCategory(Request $request)
    {
         $business_id = $request->business_type_id;

        $category  = Mst_categories::where("business_type_id",'=',$business_id)->pluck("category_name","category_id");
         return response()->json($category);

    }
       public function GetSubCategory(Request $request)
    {
         $cat_id = $request->product_cat_id;

        $subcategory  = Mst_categories::where("parent_id",'=',$cat_id)->pluck("category_name","category_id");
         return response()->json($subcategory);

    }


public function storeProduct(Request $request,Mst_store_product $product,Mst_store_product_varient $varient_product, Mst_product_image $product_img)
{
    //dd($request->all());
    $store_id =  Auth::guard('store')->user()->store_id;

    if(isset($request->product_name))
    {
      $s = DB::table('mst_store_products')
      ->where('product_name','LIKE', '%'.$request->product_name.'%')
      ->where('store_id',$store_id)
      ->groupBy('product_name')->count();
      if($s > 0)
      {
        return redirect()->back()->withErrors(['store' => 'Product name already exist'])->withInput();
      }
    }


    $validator = Validator::make($request->all(),
        [
           // 'product_name'          => 'required|unique:mst_store_products,product_name,'.$store_id.',store_id',
            'product_name'          => 'required',
            'product_description'   => 'required',
            'regular_price'   => 'required',
            'sale_price'   => 'required',
            'tax_id'   => 'required',
            'min_stock'   => 'required',
            'product_code'   => 'required',
            'business_type_id'   => 'required',
            'attr_group_id'   => 'required',
            'attr_value_id'   => 'required',
            'product_cat_id'   => 'required',
            'vendor_id'   => 'required',
            'color_id'   => 'required',
           // 'product_image.*' => 'required|dimensions:min_width=1000,min_height=800',
            'product_image.*' => 'required',



         ],
        [

            'product_name.required'             => 'Product name required',
            'product_name.unique'             => 'Product name already exist',
            'product_description.required'      => 'Product description required',
            'regular_price.required'      => 'Regular price required',
            'sale_price.required'      => 'Sale price required',
            'tax_id.required'      => 'Tax required',
            'min_stock.required'      => 'Minimum stock required',
            'product_code.required'      => 'Product code required',
            'business_type_id.required'        => 'Product type required',
            'attr_group_id.required'        => 'Attribute group required',
            'attr_value_id.required'        => 'Attribute value required',
            'product_cat_id.required'        => 'Product category required',
            'vendor_id.required'        => 'Vendor required',
            'color_id.required'        => 'Color required',
            'product_image.required'        => 'Product image required',
            'product_image.dimensions'        => 'Product image dimensions invalid',


      ]);

    if(!$validator->fails())
       {


        $product->product_name           = $request->product_name;
        $product->product_description    = $request->product_description;
        $product->product_price          = $request->regular_price;
        $product->product_price_offer    = $request->sale_price;
        $product->tax_id                 = $request->tax_id; // new
        $product->stock_count                 = $request->min_stock; // stock count

        //$product->product_code          = "PRDCT00"; // old
        $product->product_code           = $request->product_code;
        $product->business_type_id       = $request->business_type_id; // product type
        $product->color_id               = $request->color_id; // new

        $product->attr_group_id          = $request->attr_group_id;
        $product->attr_value_id          = $request->attr_value_id;
        $product->product_cat_id         = $request->product_cat_id;
        $product->vendor_id              = $request->vendor_id; // new

        $product->product_name_slug      = Str::of($request->product_name)->slug('-');
       // $product->product_specification  = $request->product_specification;  // removed
        $product->store_id               = $store_id;

       // $product->product_offer_from_date = $request->product_offer_from_date;
       // $product->product_offer_to_date   = $request->product_offer_to_date;
       // $product->product_delivery_info   = $request->product_delivery_info;
       //  $product->product_shipping_info   =$request->product_shipping_info;

        if($request->min_stock == 0)
        {
            $product->stock_status = 0;
        }else
        {
             $product->stock_status = 1;
        }

        // $product->product_commision_rate     = 3.5;
        $product->product_status         = 1;


// old s
            // if($request->hasFile('product_base_image'))
            // {
            //     $product_image = $request->file('product_base_image');

            //     $filename = time().'.'.$product_image->getClientOriginalExtension();

            //     $destination_path = 'assets/uploads/products/base_product/base_image';

            //     $product_img = Image::make($product_image->getRealPath());
            //     $product_img->save($destination_path.'/'.$filename,80);
            //     $product->product_base_image = $filename;

            //    //dd($filename);
            // }
// old e

           $product->save();
            $id = DB::getPdo()->lastInsertId();
// old s

            //   $product_code = "PRDCT00".''.$id;

           //   DB::table('mst_store_products')->where('product_id', $id)->update(['product_code' => $product_code]);


            // $varient_product->product_varient_price           = $request->product_price;
            //  $varient_product->product_varient_offer_price    = $request->product_price_offer;
            //  $varient_product->product_varient_offer_from_date = $request->product_offer_from_date;
            //  $varient_product->product_varient_offer_to_date   = $request->product_offer_to_date;

            //  $varient_product->product_id                      = $id;
            //  $varient_product->store_id                        = $store_id;
            //  $varient_product->attr_group_id                 = $request->attr_group_id;
            //  $varient_product->attr_value_id                 = $request->attr_value_id;

            //  if($request->hasFile('product_base_image'))
            //  {
            //     $product_image = $request->file('product_base_image');

            //      $filename1 = time().'.'.$product_image->getClientOriginalExtension();

            //      $destination_path = 'assets/uploads/products/varient_product/base_image';

            //      $product_img = Image::make($product_image->getRealPath());
            //      $product_img->save($destination_path.'/'.$filename1,80);
            //      $varient_product->product_varient_base_image  = $filename1;

            //      }

             //    $varient_product->save();


                // $varient_id = DB::getPdo()->lastInsertId();
// old e

                  if ($request->hasFile('product_image')) {
                  $allowedfileExtension = ['jpg', 'png', 'jpeg',];
                  $files = $request->file('product_image');
                  $c = 1;
                  foreach ($files as $file) {



                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();

                   // $fullpath = $filename . '.' . $extension ;
                    $file->move('assets/uploads/products/base_product/base_image', $filename);
                       $date = Carbon::now();
                        $data1= [[
                                'product_image'      => $filename,
                                'product_id' => $id,
                                'product_varient_id' => $id,
                                'image_flag'         => 1,
                                'created_at'         => $date,
                                'updated_at'         => $date,
                                    ],
                                  ];

                             Mst_product_image::insert($data1);

                    if($c == 1)
                    {
                        DB::table('mst_store_products')->where('product_id', $id)
                        ->update(['product_base_image' => $filename]);
                        $c++;

                    }

                }
              }

// old s

           /*
                 $date = Carbon::now();

                if($request->hasFile('product_image'))
                {
                    $product_image = $request->file('product_image');
                    //dd($product_image);
                    foreach($product_image as $image)
                    {
                        $filename[] = time().'.'.$image->getClientOriginalExtension();
                        dd($filename);
                        $destination_path = 'assets/uploads/products/base_product/feature_image';

                        $product_img = Image::make($image->getRealPath());
                        $product_img->save($destination_path.'/'.$filename,80);

                            $data1= [[
                                'product_image'      => $filename,
                                'product_varient_id' => $varient_id,
                                'image_flag'         => 1,
                                'created_at'         => $date,
                                'updated_at'         => $date,
                                    ],
                                  ];

                             Mst_product_image::insert($data1);

                    }
                }
                */
               /* $product_img->product_image = $filename1;
                $product_img->product_varient_id = $varient_id;
                $product_img->image_flag = 0;
                $product_img->save();*/
// old e


         return redirect('store/product/list')->with('status','Product added successfully.');

        }else
            {

                return redirect()->back()->withErrors($validator)->withInput();
            }

}
 public function viewProduct(Request $request, $id)
    {
        $pageTitle = "View Product";

        $product = Mst_store_product::where('product_name_slug', '=',$id)->first();
        $product_id = $product->product_id;

      // $varient_product = Mst_store_product_varient::where('product_id', '=',$product_id)->first();

       //$product_varient_id = $varient_product->product_varient_id;

       // $attr_groups = Mst_attribute_group::all();
        $product_images = Mst_product_image::where('product_id','=',$product_id)->get();

        //dd($product_images);

     //   $store = Mst_store::all();
       // $categories = Mst_categories::where([['category_status', '=', '1'],['parent_id', '==', '0'],])->whereIn('category_id',['1','4','9'])->get();


        return view('store.elements.product.view',compact('product','pageTitle','product_images'));

    }
 public function editProduct(Request $request, $id)
    {
        $pageTitle = "Edit Product";

        $product = Mst_store_product::where('product_name_slug', '=',$id)->first();
        $product_id = $product->product_id;

      // $varient_product = Mst_store_product_varient::where('product_id', '=',$product_id)->first();

      // $product_varient_id = $varient_product->product_varient_id;
       $business_types = Mst_business_types::all();
        $attr_groups = Mst_attribute_group::all();
        $product_images = Mst_product_image::where('product_id','=',$product_id)->get();
        $tax = Mst_Tax::all();

        $colors = Mst_attribute_value::join('mst_attribute_groups','mst_attribute_groups.attr_group_id','=','mst_attribute_values.attribute_group_id')
        ->where('mst_attribute_groups.group_name','LIKE','%color%')
        ->select('mst_attribute_values.*')
        ->get();
    $agencies = Mst_store_agencies::all();


        $store = Mst_store::all();

        return view('store.elements.product.edit',compact('agencies','colors','tax','product','pageTitle','attr_groups','store','product_images','business_types'));

    }

 public function destroyProductImage(Request $request, $product_image_id,Mst_product_image $pro_image)
 {
       // echo $product_image_id;die;
        $pro_image = Mst_product_image::where('product_image_id','=',$product_image_id);
        $pro_image->delete();

        return redirect()->back()->with('status','Product Image Deleted Successfully.');


 }




public function updateProduct(Request $request,$product_id,Mst_store_product_varient $varient_product)
{

  $store_id =  Auth::guard('store')->user()->store_id;
   $product_id = $request->product_id;
        //echo $product_id;die;
        // $product = Mst_store_product::where($product_id);
       //  $varient_product = Mst_store_product_varient::where('product_id','=',$product_id)->first();
        //dd($product_id);

        // if(isset($request->product_name))
        // {
        //   $s = DB::table('mst_store_products')
        //   ->where('product_name','LIKE', '%'.$request->product_name.'%')
        //   ->where('store_id',$store_id)
        //   ->groupBy('product_name')->count();
        //   if($s > 0)
        //   {
        //     return redirect()->back()->withErrors(['store' => 'Product name already exist'])->withInput();
        //   }
        // }

        $validator = Validator::make($request->all(),
        [
           // 'product_name'          => 'required|unique:mst_store_products,product_name,'.$product_id.',product_id',
           'product_name'   => 'required',
           'product_description'   => 'required',
            'regular_price'   => 'required',
            'sale_price'   => 'required',
            'tax_id'   => 'required',
            'min_stock'   => 'required',
            'product_code'   => 'required',
            'business_type_id'   => 'required',
            'attr_group_id'   => 'required',
            'attr_value_id'   => 'required',
            'product_cat_id'   => 'required',
            'vendor_id'   => 'required',
            'color_id'   => 'required',
          //  'product_image.*' => 'dimensions:min_width=1000,min_height=800',



         ],
        [

          'product_name.required'             => 'Product name required',
          'product_name.unique'             => 'Product name already exist',
          'product_description.required'      => 'Product description required',
          'regular_price.required'      => 'Regular price required',
          'sale_price.required'      => 'Sale price required',
          'tax_id.required'      => 'Tax required',
          'min_stock.required'      => 'Minimum stock required',
          'product_code.required'      => 'Product code required',
          'business_type_id.required'        => 'Product type required',
          'attr_group_id.required'        => 'Attribute group required',
          'attr_value_id.required'        => 'Attribute value required',
          'product_cat_id.required'        => 'Product category required',
          'vendor_id.required'        => 'Vendor required',
          'color_id.required'        => 'Color required',
          'product_image.required'        => 'Product image required',
          'product_image.dimensions'        => 'Product image dimensions invalid',


      ]);

    if(!$validator->fails())
       {



        $product['product_name']          = $request->product_name;
        $product['product_description']    = $request->product_description;
        $product['product_price']         = $request->regular_price;
        $product['product_price_offer']    = $request->sale_price;
        $product['tax_id']                 = $request->tax_id; // new
        $product['stock_count']                = $request->min_stock; // stock count
        $product['product_code']          = $request->product_code;
        $product['business_type_id']       = $request->business_type_id; // product type
        $product['color_id']               = $request->color_id; // new

        $product['attr_group_id']         = $request->attr_group_id;
        $product['attr_value_id']         = $request->attr_value_id;
        $product['product_cat_id']         = $request->product_cat_id;
        $product['vendor_id']            = $request->vendor_id; // new

        $product['product_name_slug']      = Str::of($request->product_name)->slug('-');
        $product['store_id']               = $store_id;

        if($request['min_stock'] == 0)
        {
            $product['stock_status'] = 0;
        }else
        {
             $product['stock_status'] = 1;
        }

       // $product['product_status'] = 0;


   DB::table('mst_store_products')->where('product_id', $product_id)->update($product);

// adding product images
            if ($request->hasFile('product_image')) {
               // echo "here";die;
                $allowedfileExtension = ['jpg', 'png', 'jpeg',];
                $files = $request->file('product_image');
                foreach ($files as $file) {



                  $filename = $file->getClientOriginalName();
                  $extension = $file->getClientOriginalExtension();

                 // $fullpath = $filename . '.' . $extension ;
                  $file->move('assets/uploads/products/base_product/base_image', $filename);
                     $date = Carbon::now();
                      $data1= [[
                              'product_image'      => $filename,
                              'product_id' => $product_id,
                              'product_varient_id' => $product_id,
                              'image_flag'         => 1,
                              'created_at'         => $date,
                              'updated_at'         => $date,
                                  ],
                                ];

                           Mst_product_image::insert($data1);



              }
            }

            return redirect('store/product/list')->with('status','Product Updated Successfully.');

        }else
            {

                return redirect()->back()->withErrors($validator)->withInput();
            }

}
public function destroyProduct(Request $request,Mst_store_product $product)
    {

       $product->delete();

        return redirect('store/product/list')->with('status','Product deleted Successfully');
    }

public function statusProduct(Request $request,Mst_store_product $product,$product_id)
    {

         $pro_id = $request->product_id;
         $product = Mst_store_product::Find($pro_id);
         $status = $product->product_status;

         if($status == 0)
         {
             $product->product_status  = 1;

         }else
         {

        $product->product_status  = 0;


         }
        $product->update();

     return redirect()->back()->with('status','Product Status Changed Successfully');
    }

    public function stockUpdate(Request $request,
                                 Mst_store_product $product, $product_id)
    {


        $product_id = $request->product_id;
        $product = Mst_store_product::Find($product_id);

        $validator = Validator::make($request->all(),
        [

            'stock_count'   => 'required',

         ],
        [
            'stock_count.required' => 'Status required',


        ]);
      // $this->uploads($request);
        if(!$validator->fails())
        {
        $data= $request->except('_token');


            $product->stock_count = $request->stock_count;
            if($request->stock_count == 0)
            {
                $product->stock_status = 0;
            }else
            {
                 $product->stock_status = 1;
            }

        $product->update();

        return redirect()->back()->with('status','Stock Updated successfully.');
    }else
    {
        return redirect()->back()->withErrors($validator)->withInput();
    }
}



     public function listAgency(Request $request)
  {


    $pageTitle = "Agencies";
    $user_id =   Auth::guard('store')->user()->store_id;
    $agencies = Mst_store_link_agency::where('store_id','=',$user_id)->get();
    $countries = Country::all();

    return view('store.elements.agencies.list',compact('agencies','pageTitle','countries'));

    }

     public function  AssignAgency(Request $request)
    {

    $pageTitle = "Create Agency";

     $agencies = Mst_store_agencies::all();

    return view('store.elements.agencies.assign_agency',compact('agencies','pageTitle'));

    }

    public function storeAssignAgency(Request $request,Mst_store_link_agency $link_agency)
    {

      $validator = Validator::make($request->all(),
    [
        'agency_id'             => 'required',

         ],
    [
        'agency_id.required'       => 'Agency required',



    ]);

        if(!$validator->fails())
    {
      $data= $request->except('_token');

      $store_id = Auth()->guard('store')->user()->store_id;
      $date =  Carbon::now();
      $values = $request->agency_id;
        //dd($values);
      foreach($values as $value){

          $data = [[
             'agency_id'=>$value,
             'store_id'=>$store_id,
             'created_at'=> $date,
             'updated_at'=> $date,


          ],
            ];

    Mst_store_link_agency::insert($data);
    }

       return redirect('store/agency/list')->with('status','Agency Assigned successfully.');
  }else
  {

    return redirect()->back()->withErrors($validator)->withInput();
  }


    }
  public function CheckAgencyEmail(Request $request)
    {

      $email = $request->agency_email_address;

        $data = Mst_store_agencies::where('agency_email_address', $email)
          ->count();

        if($data >0)
        {
         echo 'not_unique';
        }
        else
        {
         echo 'unique';
        }
     }

     public function CheckAgencyUsername(Request $request)
    {

      $username = $request->agency_username;
        $data = Mst_store_agencies::where('agency_username', $username)
          ->count();

        if($data >0)
        {
          //dd()
         echo 'not_unique';
        }
        else
        {
         echo 'unique';
        }
     }
     public function GetState(Request $request)
    {
        $country_id = $request->country_id;
        //dd($country_id);
        $state = State::where("country_id",'=',$country_id)
        ->pluck("state_name","state_id");
        return response()->json($state);
    }

    public function GetTown(Request $request)
		{
				$city_id = $request->city_id;
				//dd($city_id);
				$town = Town::where("district_id",'=',$city_id)
				->pluck("town_name","town_id");
			//	echo $town;die;
			 	return response()->json($town);
		}

  public function GetCity(Request $request)
    {
        $state_id = $request->state_id;
        //dd($state_id);
        $city = District::where("state_id",'=',$state_id)
        ->pluck("district_name","district_id");
        return response()->json($city);
    }

     public function createAgency()
      {

          $pageTitle = "Create Agencies";
          $agencies = Mst_store_agencies::all();
          $countries   = Country::all();
          $business_types = Mst_business_types::all();

          return view('store.elements.agencies.create',compact('pageTitle','agencies','countries','business_types'));


      }


  public function storeAgency(Request $request, Mst_store_agencies $agency)
    {

  $validator = Validator::make($request->all(),
    [
        'agency_name'                 => 'required|unique:mst_store_agencies',
      'agency_contact_person_name'        => 'required',
      'agency_contact_person_phone_number'=> 'required',
   // 'agency_contact_number_2'           => 'required',
      'agency_website_link'             => 'required',
      'agency_pincode'            => 'required',
      'agency_primary_address'            => 'required',
      'agency_email_address'              => 'required',
      'country_id'                  => 'required',
      'state_id'                      => 'required',
      'district_id'                       => 'required',
      'agency_username'             => 'required|unique:mst_store_agencies',
      'agency_password'                 => 'required|min:5|same:password_confirmation',
      'agency_logo'             => 'required|mimes:jpeg,png,jpg,gif,svg'


         ],
    [
        'agency_name.required'                 => 'Agency name required',
      'agency_contact_person_name.required'        => 'Contact person name required',
      'agency_contact_person_phone_number.required' => 'Contact person Number required',
      //'agency_contact_number_2.required'            => 'Contact number 2 required',
      'agency_website_link.required'             => 'website Link required',
      'agency_pincode.required'              => 'Pincode required',
      'agency_primary_address.required'             => 'Primary address required',
      'agency_email_address.required'               => 'Email required',
      'country_id.required'                       => 'Country required',
      'state_id.required'                       => 'State required',
      'district_id.required'                      => 'District  required',
      'agency_username.required'                => 'Username required',
      'agency_password.required'            => 'Password required',
      'agency_logo.required'              =>'Agency logo required'


    ]);

        if(!$validator->fails())
    {
      $data= $request->except('_token');


    $agency->agency_name      = $request->agency_name;
    $agency->agency_name_slug     = Str::of($request->agency_name)->slug('-');
    $agency->agency_contact_person_name   = $request->agency_contact_person_name;
    $agency->agency_contact_person_phone_number =    $request->agency_contact_person_phone_number;
    $agency->agency_contact_number_2       = $request->agency_contact_number_2;
    $agency->agency_website_link           = $request->agency_website_link;
    $agency->agency_pincode                = $request->agency_pincode;
    $agency->agency_primary_address        = $request->agency_primary_address;
    $agency->agency_email_address          = $request->agency_email_address;
    $agency->country_id                    = $request->country_id;
    $agency->state_id                      = $request->state_id;
    $agency->district_id                   = $request->district_id;
    $agency->business_type_id              = $request->business_type_id;
    $agency->agency_username               = $request->agency_username;
    $agency->agency_password               = Hash::make($request->agency_password);
    $agency->agency_account_status         = 0;





  if($request->hasFile('agency_logo'))
    {
      $agency_logo = $request->file('agency_logo');


      $filename = time().'.'.$agency_logo->getClientOriginalExtension();

      $location = public_path('assets/uploads/agency/logos/'.$filename);

      Image::make($agency_logo)->save($location);
      $agency->agency_logo = $filename;

    }

      $agency->save();
        return redirect('store/agency/list')->with('status','Agency added successfully.');
  }else
  {

    return redirect()->back()->withErrors($validator)->withInput();
  }

  }

  public function listOrder(Request $request)
  {

    $pageTitle = "List Order";
    $store_id =   Auth::guard('store')->user()->store_id;

    $orders = Trn_store_order::where('store_id','=',$store_id)->orderBy('order_id', 'DESC')
    ->get();
    $status = Sys_store_order_status::all();
    $store = Mst_store::all();
    $product = Mst_store_product::where('store_id','=',$store_id)->get();
    $delivery_boys = Mst_delivery_boy::where('store_id','=',$store_id)->get();

    if($_GET){

      $delivery_boy_id = $request->delivery_boy_id;
      $status_id = $request->status_id;


      $a1 = Carbon:: parse($request->date_from)->startOfDay();
      $a2  = Carbon:: parse($request->date_to)->endOfDay();
      DB::enableQueryLog();

    $query = Trn_store_order::where('store_id','=',$store_id);

    if(isset($request->status_id))
    {
       $query->where('status_id', $status_id);
     //  $query->orWhere('payment_status', $status_id);
    }
    if(isset($request->delivery_boy_id))
    {
       $query->where('delivery_boy_id',$delivery_boy_id);
    }
    if(isset($request->date_from) && isset($request->date_to))
    {
      $query->whereDate('created_at','>=',$a1)->whereDate('created_at','<=',$a2);
    }
          
            $orders = $query->get();
           // dd(DB::getQueryLog());

    return view('store.elements.order.list',compact('orders','pageTitle','status','store','status','product','delivery_boys'));

      }

  return view('store.elements.order.list',compact('orders','pageTitle','status','store','status','product','delivery_boys'));

    }
     public function viewOrder(Request $request,$id)
    {
      try{ 
        $pageTitle = "View Order";
        $decrId  = Crypt::decryptString($id);
        $order = Trn_store_order::Find($decrId);
        $order_items = Trn_store_order_item::where('order_id',$decrId)->get();

        $product = $order->product_id;

        $subadmin_id = Auth()->guard('store')->user()->subadmin_id;


        $delivery_boys = \DB::table('mst_delivery_boys')
          ->where('subadmin_id',$subadmin_id)
          ->get();

        $customer = Trn_store_customer::all();
        $status = Sys_store_order_status::all();

        return view('store.elements.order.view',compact('delivery_boys','order_items','order','pageTitle','status','customer'));
        
        } catch (\Exception $e) {
          
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
      
        }
    }
    public function updateOrder(Request $request,$id)
    {
      try{ 

       // dd($request->all());
       

        $data['delivery_boy_id']  = $request->delivery_boy_id;
        $data['status_id']  = $request->status_id;
        $data['order_note']  = $request->order_note;

		    $query = Trn_store_order::where('order_id',$id)->update($data);

        return redirect('store/order/list')->with('status','Order updated successfully.');

      } catch (\Exception $e) {
        //echo $e->getMessage();die;
        return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
      }
      
    }
    

  public function viewInvoice(Request $request,$id)
  {

    $pageTitle = "View Invoice";
    $decrId  = Crypt::decryptString($id);
    $order = Trn_store_order::Find($decrId);
    $customer = Trn_store_customer::all();
    $status = Sys_store_order_status::all();
    $order_items = Trn_store_order_item::where('order_id',$decrId)->get();


    return view('store.elements.order.invoice',compact('order_items','order','pageTitle','status','customer'));

    }
      public function OrderStatus(Request $request, Trn_store_order $order, $order_id)
    {

      try {

      
          $order_id = $request->order_id;
          $order = Trn_store_order::Find($order_id);
          $order_number = $order->order_number;

          $validator = Validator::make($request->all(),
          [

            'status_id'   => 'required',

              ],
          [
              'status_id.required' => 'Status required',


          ]);

            if(!$validator->fails())
        {
          $data= $request->except('_token');


          $order->status_id = $request->status_id;

          $status_id = $request->status_id;
        // dd($status_id);
          if($status_id == 1)
          {
              $order_status = "Pending";
          }elseif ($status_id == 2) {
              $order_status = "PaymentSuccess";
          }elseif ($status_id == 3) {
              $order_status = "Payment Cancelled";
          }elseif ($status_id == 4) {
              $order_status = "Confirmed";
          }elseif ($status_id == 5) {
              $order_status = "Cancelled";
          }
          elseif ($status_id == 6) {
              $order_status = "Completed";
          }
          elseif ($status_id == 7) {
              $order_status = "Shipped";
          }
          elseif ($status_id == 8) {
              $order_status = "Out for Delivery";
          }else  {
              $order_status = "Deliverd";
          }

        $cus_id = $order->customer_id;

        $customer = Trn_store_customer::Find($cus_id);
        $customer_email = $customer->customer_email;
          //dd($customer_email);
          $order->update();

          $data = array('order_number'=>$order_number,'order_status'=>$order_status,'to_mail'=>$customer_email);


            // Mail::send('store/mail-template/order-status-mail-template', $data, function($message) use ($data){
            //         $message->to($data['to_mail'], 'Yellowstore - Order Status')->subject
            //             ('ORDER-STATUS-UPDATION');
            //         $message->from('anumadathinakath@gmail.com','Customer-Order-Status');
            //     });

            return redirect()->back()->with('status','Status updated successfully.');
      }else
      {
        return redirect()->back()->withErrors($validator)->withInput();
      }
    } catch (\Exception $e) {
      
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();

  }
}

 public function AssignOrder(Request $request, $id)
    {


        $pageTitle = "Assign Order to Delivery Boy";
        $store_id = Auth()->guard('store')->user()->store_id;
        $decrId  = Crypt::decryptString($id);
        $order = Trn_store_order::Find($decrId);
        $delivery_boys = Mst_delivery_boy::where('store_id','=',$store_id)->get();

    return view('store.elements.order.assign_order',compact('order','pageTitle','delivery_boys'));

    }

    public function storeAssignedOrder(Request $request,Mst_order_link_delivery_boy $link_delivery_boy)
    {


     $order_id = $request->order_id;
      $validator = Validator::make($request->all(),
    [
        'delivery_boy_id'             => 'required',

         ],
    [
        'delivery_boy_id.required'       => 'Delivery boy required',



    ]);

        if(!$validator->fails())
    {
      $data= $request->except('_token');

    $link_delivery_boy->order_id = $request->order_id;
     $link_delivery_boy->delivery_boy_id = $request->delivery_boy_id;
     $link_delivery_boy->save();


       return redirect('store/order/list')->with('status','Order assigned successfully.');
  }else
  {

    return redirect()->back()->withErrors($validator)->withInput();
  }
}

 public function generatePdf(Request $request,$id)
 {


    $decrId  = Crypt::decryptString($id);
    $order = Trn_store_order::Find($decrId);
    $order_no = $order->order_number;
    $pageTitle = "Invoice";
    $order_items = Trn_store_order_item::where('order_id',$decrId)->get();

   // dd($order_no);

    $pdf = PDF::loadView('store.elements.order.bill', compact('order_items','order','pageTitle'));

  //return view('store.elements.order.bill',compact('order_items','pageTitle','order'));


    $content =  $pdf->download()->getOriginalContent();

    Storage::put('uploads\order_invoice\Ivoice_'.$order_no.'.pdf', $content);

    return $pdf->download('Ivoice_'.$order_no.'.pdf');

 }
 public function SendInvoice(Request $request,$id)
 {


    $decrId  = Crypt::decryptString($id);
    $order = Trn_store_order::Find($decrId);
    $order_no = $order->order_number;
    $cus_id = $order->customer_id;
    $customer = Trn_store_customer::where('customer_id','=',$cus_id)->first();
    $cus_mobile_number = $customer->customer_mobile_number;

    $file =  Storage::get('uploads\order_invoice\Ivoice_'.$order_no.'.pdf');


  dd($file);

 }

public function destroyAttribute(Request $request,Mst_attribute_group $attr_groups)
{
        $attr_groups->delete();

        return redirect()->back()->with('status','Attribute deleted successfully');
}


  public function storeAttribute(Request $request, Mst_attribute_group $attr_group)
    {

  $validator = Validator::make($request->all(),
    [
        'group_name'                 => 'required',


         ],
    [
        'group_name.required'                 => 'Group name required',


    ]);

        if(!$validator->fails())
    {
      $data= $request->except('_token');


    $attr_group->group_name      = $request->group_name;

      $attr_group->save();
        return redirect()->back()->with('status','Attribute added successfully.');
  }else
  {

    return redirect()->back()->withErrors($validator)->withInput();
  }

  }
public function listAttributeGroup()
{

  $pageHeading= "attribute_group";
  $pageTitle = "List Attribute Group";
  $attributegroups = Mst_attribute_group::all();

  return view('store.elements.attribute_group.list',compact('attributegroups','pageTitle','pageHeading'));
}



  public function editAttributeGroup(Request $request, $id)
  {

  $decryptId = Crypt::decryptString($id);


  $pageTitle = "Edit Attribute Group";
    $attributegroup = Mst_attribute_group::Find($decryptId);

    return view('store.elements.attribute_group.edit',compact('attributegroup','pageTitle'));



      }

     public function updateAtrGroup(Request $request,
                   Mst_attribute_group $attributegroup, $attr_group_id)
    {

      $GrpId = $request->attr_group_id;
      $attributegroup = Mst_attribute_group::Find($GrpId);

    $validator = Validator::make($request->all(),
    [
      'group_name'   => 'required',

         ],
    [
        'group_name.required'        => 'Group name required',


    ]);

        if(!$validator->fails())
    {
      $data= $request->except('_token');

    $attributegroup->group_name  = $request->group_name;


    $attributegroup->update();

         return redirect('store/attribute_group/list')->with('status','Attribute group updated successfully.');
  }else
  {

    return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  public function listAttr_Value()
  {

    $pageTitle = "List Attribute Value";
    $attributevalues = Mst_attribute_value::all();
    $attributegroups = Mst_attribute_group::all();

    return view('store.elements.attribute_value.list',compact('attributevalues','pageTitle','attributegroups'));


      }

 public function createAttr_Value(Request $request,Mst_attribute_value $attribute_value)
  {


    $pageTitle = "Create Attribute Value";
    $attributevalues = Mst_attribute_value::all();
    $attributegroups = Mst_attribute_group::all();

    //$attr_grps    = $request->$attribute_group_id;
    return view('store.elements.attribute_value.create',compact('attributevalues','pageTitle','attributegroups'));


      }

  public function storeAttr_Value(Request $request, Mst_attribute_value $attribute_value)
    {

      $validator = Validator::make($request->all(),
      [
          'group_value'       => 'required',
          'attribute_group_id' =>'required',

          ],
      [
          'group_value.required'          => 'Attribute value required',
          'attribute_group_id.required|nimeric' => 'Select group of attribute'


      ]);
      // $this->uploads($request);
        if(!$validator->fails())
          {
            $data= $request->except('_token');

          $values = $request->group_value;

            //dd($values);
            $attr_grp_value = $request->attribute_group_id;
            $Hexvalue = $request->Hexvalue;
            $group_value = $request->group_value;
            $status = 1;
            $date =  Carbon::now();
            // dd($date);
            if($attr_grp_value == 2)
            {
              if($Hexvalue)
              {
                $count = count($Hexvalue);
                //dd($count);

                //$countvalue = 2;
                for($i=0;$i<$count;$i++){

                $attribute_value = new Mst_attribute_value;
                $attribute_value->attribute_group_id = $attr_grp_value;
                $attribute_value->attr_value_status = $status;
                $attribute_value->group_value = $request->group_value[$i];
                $attribute_value->Hexvalue = $Hexvalue[$i];
                $attribute_value->created_at = $date;
                $attribute_value->updated_at = $date;

                $attribute_value->save();

            }
          }


      }else{

          foreach($values as $value){

          $data = [[
             'group_value'=>$value,
             'attribute_group_id'=>$request->attribute_group_id,
             'attr_value_status'=>1,
             'created_at'=> $date,
           'updated_at'=> $date,


          ],
            ];
        //dd($data);

    Mst_attribute_value::insert($data);
          }

      }

        return redirect('store/attribute_value/list')->with('status','Attribute added successfully.');
  }else
  {
    //return redirect('/')->withErrors($validator->errors());
    return redirect()->back()->withErrors($validator)->withInput();
  }

  }
  public function editAttr_Value(Request $request, $id)
  {

  $decryptId = Crypt::decryptString($id);

  $pageTitle = "Edit Attribute Value";
    $attributevalue = Mst_attribute_value::Find($decryptId);
    $attributegroups = Mst_attribute_group::all();

    return view('store.elements.attribute_value.edit',compact('attributevalue','attributegroups','pageTitle'));


      }

  public function updateAttr_Value(Request $request,
                   Mst_attribute_value $attributevalue, $attr_value_id)
    {

      $GrpId = $request->attr_value_id;
      $attributevalue = Mst_attribute_value::Find($GrpId);

  $validator = Validator::make($request->all(),
    [
      'group_value'   => 'required',
      'attribute_group_id' =>'required',

         ],
    [
        'group_value.required'        => 'Group value required',
        'attribute_group_id'          => 'Group name required'


    ]);
      // $this->uploads($request);
        if(!$validator->fails())
    {
      $data= $request->except('_token');

    $attributevalue->group_value  = $request->group_value;
    $attributevalue->attribute_group_id  = $request->attribute_group_id;
    if($request->attribute_group_id == 2)
    {
      $attributevalue->Hexvalue  = $request->Hexvalue;
    }

    $attributevalue->update();
  //dd($fetch);
         return redirect('store/attribute_value/list')->with('status','Attribute value updated successfully.');
  }else
  {

    return redirect()->back()->withErrors($validator)->withInput();
  }
}
public function destroyAttr_Value(Request $request,Mst_attribute_value $attribute_value)
  {

      $delete = $attribute_value->delete();


    return redirect('store/attribute_value/list')->with('status','Attribute value deleted successfully.');;
  }
public function destroyAttr_Group(Request $request,Mst_attribute_group $attribute_group)
  {

      $delete = $attribute_group->delete();


    return redirect('store/attribute_group/list')->with('status','Attribute group deleted successfully.');;
  }

// inventory management


    public function listInventory(Request $request)
    {
        $pageTitle = "Inventory Management";
        $store_id =  Auth::guard('store')->user()->store_id;
        $products = Mst_store_product::where('store_id',$store_id)->get();

      if($_GET){
        
        $query = Mst_store_product::where('store_id',$store_id);
        if($request->product_name){
          $query = $query->where('product_name','LIKE','%'.$request->product_name.'%');
        }
        $products = $query->get();
        return view('store.elements.inventory.list',compact('products','pageTitle'));
      }

      return view('store.elements.inventory.list',compact('products','pageTitle'));

    }

    public function UpdateStock(Request $request)
    {

        $updated_stock = $request->updated_stock;
        $product_id = $request->product_id;

        if( $us = DB::table('mst_store_products')->where('product_id', $product_id)->increment('stock_count',$updated_stock))
        {
            $s = DB::table('mst_store_products')->where('product_id', $product_id)->pluck("stock_count");

            return response()->json($s);

        }else{
            echo "error";
        }

    }

    public function listPOS()
    {
      $pageTitle = "POS";
    	$store_id =   Auth::guard('store')->user()->store_id;

     $customer = Trn_store_customer::all();        
     $products = Mst_store_product::where('store_id',$store_id)->where('stock_count','!=',0)->get();
     $tax = Mst_Tax::all();

   return view('store.elements.pos.list',compact('tax','products','customer','pageTitle'));
      
    }

    
    public function findProduct(Request $request)
    {

        $product_id = $request->product_id;

        $products = DB::table('mst_store_products')->where('product_id', $product_id)->first();
     
        return response()->json($products);

    }

     public function findCustomer(Request $request)
    {

        $customer_id = $request->customer_id;

        $customer = DB::table('trn_store_customers')->where('customer_id', $customer_id)->first();
     
        return response()->json($customer);

    }

    public function savePOS(Request $request,Trn_store_order $store_order,Trn_store_order_item $order_item)
    {
    try{  
        //echo Auth::guard('store')->user()->subadmin_id;die;

        $store_order->order_number = 'ORDRYSTR00'.rand(100,999);
        $store_order->customer_id = $request->get('customer_id');
        $store_order->store_id =  Auth::guard('store')->user()->store_id;
        $store_order->subadmin_id =  Auth::guard('store')->user()->subadmin_id;
        $store_order->product_total_amount =  $request->get('full_amount');
        $store_order->payment_type_id = 1;
        $store_order->payment_status = 6;
        $store_order->status_id = 6;

        $store_order->save();
        $order_id = DB::getPdo()->lastInsertId();

        

        $invoice_info['order_id'] = $order_id;
        $invoice_info['invoice_date'] =  Carbon::now()->format('Y-m-d');
        $invoice_info['invoice_id'] = "INV0".$order_id;
        
        Trn_order_invoice::insert($invoice_info);

       // dd($data);
        $quantity = $request->get('quantity');
        $single_quantity_rate = $request->get('single_quantity_rate');
        $discount_amount = $request->get('discount_amount');
        $discount_percentage = $request->get('discount_percentage');
        $total_tax = $request->get('total_tax');
        $total_amount = $request->get('total_amount');
        
        $i = 0;

        foreach ($request->get('product_id') as $p_id) {
        //  echo "here";

          $product_detail = Mst_store_product::where('product_id','=',$p_id)->get();


          
           Mst_store_product::where('product_id','=',$p_id)->decrement('stock_count',$quantity[$i]);


          $data = [
            'order_id'=> $order_id,
            'product_id'=> $p_id,
            'customer_id'=> $request->get('customer_id'),
            'store_id'=> Auth::guard('store')->user()->store_id,
            'quantity'=> $quantity[$i],
            'unit_price'=>  $single_quantity_rate[$i],
            'tax_amount'=> $total_tax[$i],
            'total_amount'=> $total_amount[$i],
            'discount_amount'=> $discount_amount[$i],
            'discount_percentage'=> $discount_percentage[$i],
            

        ];

          
          
          Trn_store_order_item::insert($data);

        //  $order_item->save();

          $i++;
        }
       // die;
        return  redirect()->back()->with('status','Order placed successfully.');
      } catch (\Exception $e) {
      
        return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
  
    }
      }


      
      public function settings(Request $request)
      {
        $pageTitle = "Store Settings";
        $store_id =   Auth::guard('store')->user()->store_id;
        $store = Mst_store::find($store_id);
        $business_types = Mst_business_types::all();
        $districts = District::where('state_id',$store->store_state_id)->get();

        $store_settings = Trn_store_setting::where('store_id',Auth::guard('store')->user()->store_id)->get();
        $settingcount = Trn_store_setting::where('store_id',Auth::guard('store')->user()->store_id)->count();

  
        return view('store.elements.settings.create',compact('settingcount','store_settings','store','districts','pageTitle','business_types'));
        
      }

      public function updateStoreSettings(Request $request)
      {
      //  Trn_store_setting
      
      if(isset($request->start) < 1)
      {
        return redirect()->back();
      }

        $s_count = Trn_store_setting::where('store_id',Auth::guard('store')->user()->store_id)->count();
        
        if($s_count > 1)
        {
           Trn_store_setting::where('store_id',Auth::guard('store')->user()->store_id)->delete();
        }

            $i = 0;
            $start = $request->start;
            $end = $request->end;
            $delivery_charge = $request->delivery_charge;
            $packing_charge = $request->packing_charge;

            $data = [
            
              'service_area'=> $request->service_area,
              'store_district_id'=>  $request->service_district,
              'town_id'=> $request->service_town,
              'business_type_id'=> $request->business_type_id,
              
              ];

            Mst_store::where('store_id',Auth::guard('store')->user()->store_id)->update($data);

  

            

            foreach($request->start as $s)
            {
              $info = [
                'store_id'=> Auth::guard('store')->user()->store_id,
                'service_start'=> $start[$i],
                'service_end'=>  $end[$i],
                'delivery_charge'=> $delivery_charge[$i],
                'packing_charge'=> $packing_charge[$i],
                
                 ];

              Trn_store_setting::insert($info);
              $i++;
            }

        return redirect()->back()->with('status','Store settings updated successfully.');

      }

      

      public function time_slot(Request $request)
      {
        $pageTitle = "Working Days";
        $store_id =   Auth::guard('store')->user()->store_id;
        $store = Mst_store::find($store_id);

  
        $time_slots_count = Trn_StoreTimeSlot::where('store_id',Auth::guard('store')->user()->store_id)->count();
        $time_slots = Trn_StoreTimeSlot::where('store_id',Auth::guard('store')->user()->store_id)->get();

  
        return view('store.elements.time_slot.create',compact('time_slots_count','time_slots','store','pageTitle','store_id'));
        
      }

      public function delivery_time_slots(Request $request)
      {
        $pageTitle = "Time Slots";
        $store_id =   Auth::guard('store')->user()->store_id;
        $store = Mst_store::find($store_id);

        $time_slots_count = Trn_StoreDeliveryTimeSlot::where('store_id',Auth::guard('store')->user()->store_id)->count();
        $time_slots = Trn_StoreDeliveryTimeSlot::where('store_id',Auth::guard('store')->user()->store_id)->get();

        return view('store.elements.time_slot.delivery_time_slot',compact('time_slots','time_slots_count','store','pageTitle','store_id'));
        
      }

      public function update_delivery_time_slots(Request $request)
      {
       // dd($request->all());
         
        $start = $request->start;
        $end = $request->end;

        $s_count = Trn_StoreDeliveryTimeSlot::where('store_id',Auth::guard('store')->user()->store_id)->count();
        
        if($s_count > 1)
        {
          Trn_StoreDeliveryTimeSlot::where('store_id',Auth::guard('store')->user()->store_id)->delete();
        }

        
        $i = 0;
        foreach($request->start as $s)
          {
            $info = [
              'store_id'=> Auth::guard('store')->user()->store_id,
              'time_start'=>  $start[$i],
              'time_end'=> $end[$i],
              ];

              //print_r($info);die;

              Trn_StoreDeliveryTimeSlot::insert($info);
            $i++;
          }
          return  redirect()->back()->with('status','Time slots updated successfully.');

      }


      
      
      public function updateTimeSlot(Request $request)
      {
       // dd($request->all());
         
        $start = $request->start;
        $end = $request->end;
        $day = $request->day;

        $s_count = Trn_StoreTimeSlot::where('store_id',Auth::guard('store')->user()->store_id)->count();
        
        if($s_count > 1)
        {
          Trn_StoreTimeSlot::where('store_id',Auth::guard('store')->user()->store_id)->delete();
        }

        
        $i = 0;
        foreach($request->day as $s)
          {
            $info = [
              'store_id'=> Auth::guard('store')->user()->store_id,
              'day'=> $day[$i],
              'time_start'=>  $start[$i],
              'time_end'=> $end[$i],
              ];

              //print_r($info);die;

              Trn_StoreTimeSlot::insert($info);
            $i++;
          }
          return  redirect()->back()->with('status','Working days updated successfully.');

      }

// Store Admin

  public function listStoreAdmin()
  {
      $pageTitle = "List Store Admin";
      $store_admin = Trn_StoreAdmin::orderBy('store_admin_id', 'DESC')->get();
      return view('store.elements.store_admin.list',compact('store_admin','pageTitle'));
  }

  public function createStoreAdmin()
  {
      $pageTitle = "Create Store Admin";
      return view('store.elements.store_admin.create',compact('pageTitle'));
  }

  public function storeStoreAdmin(Request $request,Trn_StoreAdmin $store_admin)
  {

      $validator = Validator::make($request->all(),
      [
          'admin_name' => ['required'],
          'phone' => ['required'],
          'username' => ['required','unique:trn__store_admins'],
          'password'  => 'required|min:5|same:password_confirmation',
          'role_id' => ['required'],

      ],
      [
        'admin_name.required'         => 'Admin name required',
        'phone.required'         => 'Phone required',
        'username.required'         => 'Username required',
        'username.unique'         => 'Username exists',
        'role_id.required'         => 'Role required',
        'password.required'         => 'Password required',
		    'password.confirmed'         => 'Passwords are not matching',
      ]);
      if(!$validator->fails())
      {
          $store_admin->store_id =  Auth::guard('store')->user()->store_id;
          $store_admin->admin_name = $request->admin_name;
          $store_admin->phone = $request->phone;
          $store_admin->email = $request->email;
          $store_admin->password = Hash::make($request->password);
          $store_admin->username = $request->username;
          $store_admin->role_id = $request->role_id;
          $store_admin->status = $request->status;
          $store_admin->save();

          return redirect('store/admin/list')->with('status','Store admin added successfully.');
      }
      else
      {
          return redirect()->back()->withErrors($validator)->withInput();
      }

  }

  public function editStoreAdmin($store_admin_id)
  {
      $store_admin_id  = Crypt::decryptString($store_admin_id);
      $pageTitle = "Edit store_admin";
      $store_admin = Trn_StoreAdmin::Find($store_admin_id);
      return view('store.elements.store_admin.edit',compact('store_admin','store_admin_id','pageTitle'));
  }

  public function updateStoreAdmin(Request $request, $store_admin_id)
  {
    try{  



     // $store_a = Trn_StoreAdmin::Find($store_admin_id);

   //  echo  $password = $store_a->password; 
   //   echo $newpassword = $request->password; die;

      $validator = Validator::make($request->all(),
      [
          'admin_name' => ['required'],
          'phone' => ['required'],
          'username' => ['required'],
          //'password' => 'sometimes|same:password_confirmation',
          'role_id' => ['required'],

      ],
      [
        'admin_name.required'         => 'Admin name required',
        'phone.required'         => 'Phone required',
        'username.required'         => 'Username required',
        'role_id.required'         => 'Role required',
		 //   'password.confirmed'         => 'Passwords are not matching',
      ]);
      if(!$validator->fails())
      {
        //dd($request->all());

          $data['admin_name'] = $request->admin_name;
          $data['phone'] = $request->phone;
          $data['email'] = $request->email;
          
       
            // if($newpassword == '')
            // {
            //   $data['password'] = $password;
            // }else
            // {
            //   $data['password'] =  Hash::make($request->password);
            // }
            

          $data['username'] = $request->username;
          $data['role_id'] = $request->role_id;
          $data['status'] = $request->status;

          \DB::table('trn__store_admins')->where('store_admin_id',$store_admin_id)->update($data);


          return redirect('store/admin/list')->with('status','Store admin updated successfully.');
      }
      else
      {
          return redirect()->back()->withErrors($validator)->withInput();
      }
    } catch (\Exception $e) {
      //echo $e->getMessage();die;
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  public function removeStoreAdmin(Request $request,Trn_StoreAdmin $store_admin,$store_admin_id)
  {
    Trn_StoreAdmin::where('store_admin_id',$store_admin_id)->delete();

      return redirect()->back()->with('status','Store admin deleted successfully.');
  }




}



