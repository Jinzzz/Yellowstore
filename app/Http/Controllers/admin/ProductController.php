<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Image;
use Hash;
use DB;
use Carbon\Carbon;
use Crypt;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GlobalProductsImport;

use App\Models\admin\Mst_GlobalProducts;
use App\Models\admin\Trn_GlobalProductImage;
use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_attribute_value;
use App\Models\admin\Mst_store_agencies;
use App\Models\admin\Mst_Tax;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_product_image;
use App\Models\admin\Trn_GlobalProductVideo;


class ProductController extends Controller
{
    // Global Products

    public function listGlobalProducts()
    {
        $pageTitle = "List Global Products";
        $global_product = Mst_GlobalProducts::orderBy('global_product_id', 'DESC')->get();
        return view('admin.masters.global_product.list',compact('global_product','pageTitle'));
    }

    public function createGlobalProduct()
    {
        $pageTitle = "Create Global Product";

        $attr_groups = Mst_attribute_group::all();
        $tax = Mst_Tax::all();
        $colors = Mst_attribute_value::join('mst_attribute_groups','mst_attribute_groups.attr_group_id','=','mst_attribute_values.attribute_group_id')
        ->where('mst_attribute_groups.group_name','LIKE','%color%')
        ->select('mst_attribute_values.*')
        ->get();
        $agencies = Mst_store_agencies::all();
        $business_types = Mst_business_types::all();
        
        return view('admin.masters.global_product.create',compact('business_types','agencies','colors','tax','attr_groups','pageTitle'));
    }

    public function storeGlobalProduct(Request $request,Mst_GlobalProducts $global_product)
    {
                
      //  dd($request->all());
        
                $data = $request->except('_token');

                $validator = Validator::make($request->all(),
                [
                    'product_name' => ['required','unique:mst__global_products' ],
                    'product_description' => ['required' ],
                    'regular_price' => ['required' ],
                    'sale_price' => ['required' ],
                    'tax_id' => ['required' ],
                    'min_stock' => ['required' ],
                    'product_code' => ['required' ],
                    'business_type_id' => ['required' ],
                    'color_id' => ['required' ],
                    'product_brand' => ['required' ],
                    'attr_group_id' => ['required' ],
                    'product_cat_id' => ['required' ],
                    'vendor_id' => ['required' ],
                    'attr_value_id' => ['required' ],
                  //  'product_image.*' => ['required', 'dimensions:min_width=1000,min_height=800'],
                    'product_image.*' => ['required'],

                ],
                [
                    'product_name.required'         => 'Product name required',
                    'product_name.unique'         => 'Product name exists',
                    'product_description.required'         => 'Product description required',
                    'regular_price.required'         => 'Regular price name required',
                    'sale_price.required'         => 'Sale price name required',
                    'tax_id.required'         => 'Tax required',
                    'min_stock.required'         => 'Min stock required',
                    'product_code.required'         => 'Product code required',
                    'business_type_id.required'         => 'Business type required',
                    'color_id.required'     => 'Color required',
                    'product_brand.required'    => 'Product Brand required',
                    'attr_group_id.required'         => 'Attribute group required',
                    'attr_value_id.required'         => 'Attribute value required',
                    'product_cat_id.required'         => 'Product category required',
                    'vendor_id.required'         => 'Vendor required',
                 //   'product_image.required'        => 'Product image required',
                    'product_image.dimensions'        => 'Product image dimensions invalid',
        
                ]);
                if(!$validator->fails())
                {
                    try {

                    $global_product->product_name = $request->product_name;
                    $global_product->product_name_slug = Str::of($request->product_name)->slug('-');
                    $global_product->product_description = $request->product_description;
                    $global_product->regular_price = $request->regular_price;
                    $global_product->sale_price = $request->sale_price;
                    $global_product->tax_id = $request->tax_id;
                    $global_product->min_stock = $request->min_stock;
                    $global_product->product_code = $request->product_code;
                    $global_product->business_type_id = $request->business_type_id;
                    $global_product->color_id = $request->color_id;
                    $global_product->product_brand = $request->product_brand;
                    $global_product->attr_group_id = $request->attr_group_id;
                    $global_product->attr_value_id = $request->attr_value_id;
                    $global_product->product_cat_id = $request->product_cat_id;
                    $global_product->vendor_id = $request->vendor_id;
                    $global_product->product_base_image = $request->product_base_image; // update after image uploads
                    $global_product->created_date = Carbon::now()->format('Y-m-d');
                    $global_product->created_by = auth()->user()->id;

                    $global_product->save();
             
                    $global_product_id = DB::getPdo()->lastInsertId();

                    $k = 0; 

                        foreach($request->video_code as $vc)
                        {
                            if(isset($vc) && isset($request->platform[$k]) )
                            {
                                $data2= [[
                                    'global_product_id'   => $global_product_id,
                                    'video_code'  => $vc,
                                    'platform'  => $request->platform[$k],
                                    'created_at'  => Carbon::now(),
                                    'updated_at'  => Carbon::now(),
                                        ], ];
                                        Trn_GlobalProductVideo::insert($data2);

                            }
                        $k++;   
                        }

                    if ($request->hasFile('product_image')) {
                        $allowedfileExtension = ['jpg', 'png', 'jpeg',];
                        $files = $request->file('product_image');
                        $c = 1;
                        foreach ($files as $file) {
                          $filename = $file->getClientOriginalName();
                          $extension = $file->getClientOriginalExtension();
                            $file->move('assets/uploads/products/base_product/base_image', $filename);
                              $data1= [[
                                      'global_product_id'   => $global_product_id,
                                      'image_name'  => $filename,
                                      'created_at'  => Carbon::now(),
                                      'updated_at'  => Carbon::now(),
                                          ], ];
                            Trn_GlobalProductImage::insert($data1);
                            if($c == 1)
                            {
                                DB::table('mst__global_products')->where('global_product_id', $global_product_id)->update(['product_base_image' => $filename]);
                                $c++;
                            }
                        }
                    }
                    return redirect('/admin/global/products/list')->with('status','Global product added successfully.');
                } catch (\Exception $e) {
        
                    return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
                }
                }
                else
                {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

   
    }

    public function editGlobalProduct($global_product_id)
    {
        $global_product_id  = Crypt::decryptString($global_product_id);
        $pageTitle = "Edit Global Product";
        $product = Mst_GlobalProducts::Find($global_product_id);
        $product_images = Trn_GlobalProductImage::where('global_product_id',$global_product_id)->get();
        $videos = Trn_GlobalProductVideo::where('global_product_id',$global_product_id)->get();
        
        $attr_groups = Mst_attribute_group::all();
        $tax = Mst_Tax::all();
        $colors = Mst_attribute_value::join('mst_attribute_groups','mst_attribute_groups.attr_group_id','=','mst_attribute_values.attribute_group_id')
        ->where('mst_attribute_groups.group_name','LIKE','%color%')
        ->select('mst_attribute_values.*')
        ->get();
        $agencies = Mst_store_agencies::all();
        $business_types = Mst_business_types::all();
        
        
        return view('admin.masters.global_product.edit',compact('videos','business_types','agencies','colors','tax','attr_groups','product_images','product','global_product_id','pageTitle'));
    }

    public function updateGlobalProduct(Request $request,Mst_GlobalProducts $global_product, $global_product_id)
    {
       
        
                $validator = Validator::make($request->all(),
                [
                    'product_name' => ['required' ],
                    'product_description' => ['required' ],
                    'regular_price' => ['required' ],
                    'sale_price' => ['required' ],
                    'tax_id' => ['required' ],
                    'min_stock' => ['required' ],
                    'product_code' => ['required' ],
                    'business_type_id' => ['required' ],
                    'color_id' => ['required' ],
                    'product_brand' => ['required' ],
                    'attr_group_id' => ['required' ],
                    'product_cat_id' => ['required' ],
                    'vendor_id' => ['required' ],
                    'attr_value_id' => ['required' ],
                   // 'product_image.*' => 'dimensions:min_width=1000,min_height=800'

                ],
                [
                    'product_name.required'         => 'Product name required',
                    'product_name.unique'         => 'Product name exist',
                    'product_description.required'         => 'Product description required',
                    'regular_price.required'         => 'Regular price name required',
                    'sale_price.required'         => 'Sale price name required',
                    'tax_id.required'         => 'Tax required',
                    'min_stock.required'         => 'Min stock required',
                    'product_code.required'         => 'Product code required',
                    'business_type_id.required'         => 'Business type required',
                    'color_id.required'     => 'Color required',
                    'product_brand.required'    => 'Product Brand required',
                    'attr_group_id.required'         => 'Attribute group required',
                    'attr_value_id.required'         => 'Attribute value required',
                    'product_cat_id.required'         => 'Product category required',
                    'vendor_id.required'         => 'Vendor required',
                   // 'product_image.required'        => 'Product image required',
                    'product_image.dimensions'        => 'Product image dimensions invalid',
        
                ]);

           
        if(!$validator->fails())
        {
            try {
           
                    $data['product_name'] = $request->product_name;
                    $data['product_name_slug'] = Str::of($request->product_name)->slug('-');
                    $data['product_description'] = $request->product_description;
                    $data['sale_price'] = $request->sale_price;
                    $data['tax_id'] = $request->tax_id;
                    $data['min_stock'] = $request->min_stock;
                    $data['product_code'] = $request->product_code;
                    $data['business_type_id'] = $request->business_type_id;
                    $data['color_id'] = $request->color_id;
                    $data['product_brand'] = $request->product_brand;
                    $data['attr_group_id'] = $request->attr_group_id;
                    $data['attr_value_id'] = $request->attr_value_id;
                    $data['product_cat_id'] = $request->product_cat_id;
                    $data['vendor_id'] = $request->vendor_id;
                
                    Mst_GlobalProducts::where('global_product_id',$global_product_id)->update($data);


                    $k = 0; 

                    foreach($request->video_code as $vc)
                    {
                        if(isset($vc) && isset($request->platform[$k]) )
                        {
                            $data2= [[
                                'global_product_id'   => $global_product_id,
                                'video_code'  => $vc,
                                'platform'  => $request->platform[$k],
                                'created_at'  => Carbon::now(),
                                'updated_at'  => Carbon::now(),
                                    ], ];
                                    Trn_GlobalProductVideo::insert($data2);

                        }
                    $k++;   
                    }


                    if ($request->hasFile('product_image')) {
                        $allowedfileExtension = ['jpg', 'png', 'jpeg',];
                        $files = $request->file('product_image');
                        $c = 1;
                        foreach ($files as $file) {
                        $filename = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                            $file->move('assets/uploads/products/base_product/base_image', $filename);
                            $data1= [[
                                    'global_product_id'   => $global_product_id,
                                    'image_name'  => $filename,
                                    'created_at'  => Carbon::now(),
                                    'updated_at'  => Carbon::now(),
                                        ], ];
                            Trn_GlobalProductImage::insert($data1);
                            if($c == 1)
                            {
                                DB::table('mst__global_products')->where('global_product_id', $global_product_id)->update(['product_base_image' => $filename]);
                                $c++;
                            }
                        }
                    }
                } catch (\Exception $e) {
        
                    return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
                }


            return redirect('/admin/global/products/list')->with('status','Global product updated successfully.');
        }
        else
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

    

    }

    public function removeGlobalProduct(Request $request,$global_product_id)
    {
        Mst_GlobalProducts::where('global_product_id',$global_product_id)->delete();

        return redirect()->back()->with('status','Global product deleted successfully.');
    }

   

    public function removeGlobalProductImage(Request $request,$global_product_image_id)
    {
        Trn_GlobalProductImage::where('global_product_image_id',$global_product_image_id)->delete();

        return redirect()->back()->with('status','Product image deleted successfully.');
    }

    public function removeGlobalProductVideo(Request $request,$global_product_video_id)
    {
        Trn_GlobalProductVideo::where('global_product_video_id',$global_product_video_id)->delete();

        return redirect()->back()->with('status','Product video deleted successfully.');
    }

    public function editGlobalProductVideo($global_product_video_id)
    {
        $global_product_video_id  = Crypt::decryptString($global_product_video_id);
        $pageTitle = "Edit Global Product Video";
        $video = Trn_GlobalProductVideo::Find($global_product_video_id);
        return view('admin.masters.global_product.edit_video',compact('video','global_product_video_id','pageTitle'));
    }

    public function updateGlobalProductVideo(Request $request, $global_product_video_id)
    {
        $data = $request->except('_token');

        $validator = Validator::make($request->all(),
        [
            'platform' => ['required', ],
            'video_code' => ['required', ],

        ],
        [
            'platform.required'         => 'Platform required',
            'video_code.required'         => 'Video code required',

        ]);
        if(!$validator->fails())
        {

            $data['platform'] = $request->platform;
            $data['video_code'] = $request->video_code;

            Trn_GlobalProductVideo::where('global_product_video_id',$global_product_video_id)->update($data);


            return redirect()->back()->with('status','Video updated successfully.');
        }
        else
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function viewGlobalProduct(Request $request,$global_product_id)
    {
        $global_product_id  = Crypt::decryptString($global_product_id);
        $pageTitle = "View Global Product";
        $product = Mst_GlobalProducts::Find($global_product_id);
        $product_images = Trn_GlobalProductImage::where('global_product_id',$global_product_id)->get();
        $product_videos = Trn_GlobalProductVideo::where('global_product_id',$global_product_id)->get();
        

        return view('admin.masters.global_product.view',compact('product_videos','product_images','product','global_product_id','pageTitle'));
    }

    public function importGlobalProduct(Request $request)
    {
        $pageTitle = "Import Global Products";

        return view('admin.masters.global_product.import',compact('pageTitle'));

    }

    public function postImportGlobalProduct(Request $request)
    {
        //dd($request->all());
        
        $validator = Validator::make($request->all(),
		[
		    'products_file'       				   => 'required|mimes:xlsx',
		

        ],
		[
            'products_file.required'         => 'Products file  required',
            'products_file.mimes'         => 'Invalid file format',


            ]);
           
       //   try{ 

        $file = $request->file('products_file')->store('import');

       (new GlobalProductsImport)->import($file);
        return redirect()->back()->with('status','Global products imported successfully.');
    // } catch (\Exception $e) {
    //                return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();

    //   //  return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    // }

    }

    public function convertProduct(Request $request,$product_id,Mst_GlobalProducts $global_product)
    {
        try {

        $product = Mst_store_product::find($product_id);
		$product_image = Mst_product_image::where('product_id',$product_id)->get();
        
        $global_product->product_name = $product->product_name;
        $global_product->product_name_slug =Str::of($product->product_name)->slug('-');
        $global_product->product_description = $product->product_description;
        $global_product->regular_price = $product->product_price || 0;
        $global_product->sale_price = $product->product_price_offer || 0;
        $global_product->tax_id = $product->tax_id || 0;
        $global_product->min_stock = $product->stock_count || 0;
        $global_product->product_code = $product->product_code;
        $global_product->business_type_id = $product->business_type_id || 0;
        $global_product->color_id = $product->color_id || 0;
        $global_product->product_brand = @$product->product_brand;
        $global_product->attr_group_id = $product->attr_group_id || 0;
        $global_product->attr_value_id = $product->attr_value_id || 0;
        $global_product->product_cat_id = $product->product_cat_id || 0;
        $global_product->vendor_id = $product->vendor_id || 0;
        $global_product->product_base_image = $product->product_base_image; // update after image uploads
        $global_product->created_date = Carbon::now()->format('Y-m-d');
        $global_product->created_by = auth()->user()->id;

        $global_product->save();
                    
        $global_product_id = \DB::getPdo()->lastInsertId();


                        $c = 1; 
                        foreach ($product_image as $file) {
                          $data1= [[
                                      'global_product_id'   => $global_product_id,
                                      'image_name'  => $file->product_image,
                                      'created_at'  => Carbon::now(),
                                      'updated_at'  => Carbon::now(),
                                          ], ];
                            Trn_GlobalProductImage::insert($data1);
                            if($c == 1)
                            {
                                DB::table('mst__global_products')->where('global_product_id', $global_product_id)->update(['product_base_image' => $file->product_image]);
                                $c++;
                            }
                        }
        return redirect()->back()->with('status','Product updated to global product successfully.');
            
        } catch (\Exception $e) {
           // return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
        
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
                    
    }

    

    public function showInHome(Request $request,$product_id)
    {
        try {

        $product = Mst_store_product::find($product_id);

        if($product->show_in_home_screen == 0)
        {
            Mst_store_product::where('product_id',$product_id)->update(['show_in_home_screen' => 1]);
     
            return redirect()->back()->with('status','Offer product updated successfully.');
        }
        else
        {
            Mst_store_product::where('product_id',$product_id)->update(['show_in_home_screen' => 0]);
            return redirect()->back()->with('status','Offer product removed successfully.');
       
        }


    
        } catch (\Exception $e) {
            // return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
        
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }
    
    

}
