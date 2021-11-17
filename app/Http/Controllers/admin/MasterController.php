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

use App\Models\admin\Mst_Video;
use App\Models\admin\Mst_SubCategory;
use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Mst_RewardToCustomer;
use App\Models\admin\Trn_RewardToCustomerTemp;
use App\Models\admin\Trn_store_customer;

class MasterController extends Controller
{

// video

    public function listVideo()
    {
        $pageTitle = "List Video";
        $video = Mst_Video::all();
        return view('admin.masters.video.list',compact('video','pageTitle'));
    }

    public function createVideo()
    {
        $pageTitle = "Create Video";
        return view('admin.masters.video.create',compact('pageTitle'));
    }

    public function storeVideo(Request $request,Mst_Video $video)
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
            $video->platform = $request->platform;
            $video->video_code = $request->video_code;
            $video->status = $request->status;
            $video->save();

            return redirect('admin/video/list')->with('status','Video added successfully.');
        }
        else
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

    }

    public function editVideo($video_id)
    {
        $video_id  = Crypt::decryptString($video_id);
        $pageTitle = "Edit Video";
        $video = Mst_Video::Find($video_id);
        return view('admin.masters.video.edit',compact('video','video_id','pageTitle'));
    }

    public function updateVideo(Request $request,Mst_Video $video, $video_id)
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
            $data['status'] = $request->status;

            Mst_Video::where('video_id',$video_id)->update($data);


            return redirect('admin/video/list')->with('status','Video updated successfully.');
        }
        else
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function removeVideo(Request $request,Mst_Video $video,$video_id)
    {
        Mst_Video::where('video_id',$video_id)->delete();

        return redirect()->back()->with('status','Video deleted successfully.');
    }

//
    public function listSubCategory()
    {
        $pageTitle = "List Product Sub Category";
        $sub_category = Mst_SubCategory::orderBy('sub_category_id','DESC')->get();
      //  dd($sub_category);
        return view('admin.masters.sub_category.list',compact('sub_category','pageTitle'));
    }

    public function createSubCategory()
    {

      	$pageTitle = "Create Product Sub Category";
      	$categories = Mst_categories::where('category_status', '=', '1')->get();
		$business_types = Mst_business_types::where('business_type_status','=',1)->get();
		
        return view('admin.masters.sub_category.create',compact('pageTitle','categories','business_types'));

    }

    public function storeSubCategory(Request $request,Mst_SubCategory $sub_category)
    {
        $data = $request->except('_token');

        $validator = Validator::make($request->all(),
		[
		    'category_id'       => 'required',
		    'sub_category_name'       => 'required|unique:mst__sub_categories',
			'sub_category_icon'        => 'dimensions:width=150,height=150|image|mimes:jpeg,png,jpg',
			'sub_category_description' => 'required',
			'business_type_id'		=> 'required',


         ],
		[
		    'category_id.required'         => 'Parent category required',
		    'sub_category_name.required'         => 'Sub category name required',
			'sub_category_icon.required'        => 'Sub category icon required',
			'sub_category_icon.dimensions'        => 'Sub category icon dimensions is invalid',
			'sub_category_description.required'	 => 'Sub category description required',
			'business_type_id.required'	 => 'Business type required',

		]);

        if(!$validator->fails())
		{

     	$data= $request->except('_token');

		$sub_category->sub_category_name 		= $request->sub_category_name;
		$sub_category->sub_category_name_slug  	= Str::of($request->sub_category_name)->slug('-');
		$sub_category->sub_category_description = $request->sub_category_description;
		$sub_category->business_type_id = $request->business_type_id;
		$sub_category->category_id 		=  $request->category_id;

                if($request->hasFile('sub_category_icon'))
                {

                    $photo = $request->file('sub_category_icon');
                                $filename = time() . '.' . $photo->getClientOriginalExtension();
                                $destinationPath = 'assets/uploads/category/icons';
                                $thumb_img = Image::make($photo->getRealPath());
                                $thumb_img->save($destinationPath . '/' .$filename, 80);

         $sub_category->sub_category_icon = $filename;

                }

                $sub_category->sub_category_status 		= 1;

                $sub_category->save();

                return redirect('/admin/sub/category/list')->with('status','Sub category added successfully.');
            }else
            {
                return redirect()->back()->withErrors($validator)->withInput();
            }

    }

    public function editSubCategory($sub_category_id)
    {
     //   $sub_category_id  = Crypt::decryptString($sub_category_id);
        $pageTitle = "Edit Product Sub Category";
        $sub_category = Mst_SubCategory::where('sub_category_name_slug',$sub_category_id)->first();
        $categories = Mst_categories::where('category_status', '=', '1')->get();
		$business_types = Mst_business_types::where('business_type_status','=',1)->get();
		 return view('admin.masters.sub_category.edit',compact('business_types','categories','sub_category_id','sub_category','pageTitle'));
    }

    public function updateSubCategory(Request $request,Mst_SubCategory $sub_category, $sub_category_id)
    {
        $data = $request->except('_token');

        $validator = Validator::make($request->all(),
		[
		    'category_id'       => 'required',
		    'sub_category_name'       => 'required',
			'sub_category_icon'        => 'dimensions:width=150,height=150|image|mimes:jpeg,png,jpg',
			'sub_category_description' => 'required',
			'business_type_id'		=> 'required',


         ],
		[
		    'category_id.required'         => 'Parent category required',
		    'sub_category_name.required'         => 'Sub category name required',
			'sub_category_icon.dimensions'        => 'Sub category icon dimensions is invalid',
			'sub_category_description.required'	 => 'Sub category description required',
			'business_type_id.required'	 => 'Business type required',

		]);
        if(!$validator->fails())
        {
           
            $data['sub_category_name'] = $request->sub_category_name;
            $data['sub_category_name_slug'] = Str::of($request->sub_category_name)->slug('-');
            $data['sub_category_description'] = $request->sub_category_description;
            $data['business_type_id'] =  $request->business_type_id;
            $data['category_id'] =  $request->category_id;

            if($request->hasFile('sub_category_icon'))
            {

                $photo = $request->file('sub_category_icon');
                            $filename = time() . '.' . $photo->getClientOriginalExtension();
                            $destinationPath = 'assets/uploads/category/icons';
                            $thumb_img = Image::make($photo->getRealPath());
                            $thumb_img->save($destinationPath . '/' .$filename, 80);

                    $data['sub_category_icon'] = $filename;

            }

            Mst_SubCategory::where('sub_category_id',$sub_category_id)->update($data);


            return redirect('/admin/sub/category/list')->with('status','Sub category updated successfully.');
        }
        else
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function removeSubCategory(Request $request,Mst_SubCategory $sub_category,$sub_category_id)
    {
        Mst_SubCategory::where('sub_category_id',$sub_category_id)->delete();

        
        
        return redirect()->back()->with('status','Sub category deleted successfully.');
    }

    public function statusSubCategory(Request $request,$sub_category_id)
    {

        $sub_category = Mst_SubCategory::Find($sub_category_id);

        $status = $sub_category->sub_category_status;

        if($status == 0)
        {
             $sub_category->sub_category_status  = 1;

        }else
        {

            $sub_category->sub_category_status  = 0;

        }
        $sub_category->update();

    return redirect('/admin/sub/category/list')->with('status','Sub category status changed successfully');
    }

    public function addRewardToCustomer()
    {
        $pageTitle = "Add Reward To Customer";
       
        return view('admin.masters.customer_reward.add_customer_rewards.add',compact('pageTitle'));

    
       // return redirect()->back()->with('status','Reward added successfully.');
    }

    public function storeRewardToCustomer(Request $request,Mst_RewardToCustomer $reward,Trn_RewardToCustomerTemp $temp_reward)
    {
      //  dd($request->all());
      try{ 

            if (Trn_store_customer::where('customer_mobile_number', '=', $request->customer_mobile_number)->exists()) 
            {
               // echo "here";die;
                $reward->user_id 		= auth()->user()->id;
                $reward->customer_mobile_number  	= $request->customer_mobile_number;
                $reward->reward_discription = $request->reward_discription;
                $reward->reward_points = $request->reward_points;
                $reward->added_date 		=  Carbon::now()->format('Y-m-d');
                $reward->save(); 
            }
            else
            {
               // echo "joy";die;
              //  $temp_reward->user_id 		= auth()->user()->id;
                $temp_reward->customer_mobile_number  	= $request->customer_mobile_number;
                $temp_reward->reward_discription = $request->reward_discription;
                $temp_reward->reward_points = $request->reward_points;
                $temp_reward->added_date 		=  Carbon::now()->format('Y-m-d');
                $temp_reward->save(); 
            }

        } catch (\Exception $e) {
             //return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
        
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }

        
        return redirect('admin/list/reward-to-customer')->with('status','Customer reward added successfully');

    }
    
    public function listRewardToCustomer(Request $request)
    {
        $pageTitle = "List Rewards To Customers";

        $rewards = Mst_RewardToCustomer::orderBy('reward_to_customer_id','DESC')->get();
        $dummy_rewards = Trn_RewardToCustomerTemp::orderBy('reward_to_customer_temp_id','DESC')->get();
         
       
        return view('admin.masters.customer_reward.add_customer_rewards.list',compact('dummy_rewards','rewards','pageTitle'));

    }

    public function editRewardToCustomer(Request $request,$reward_to_customer_id)
    {
        $pageTitle = "Edit Reward To Customer";
        $reward_to_customer_id  = Crypt::decryptString($reward_to_customer_id);
        $reward = Mst_RewardToCustomer::find($reward_to_customer_id);
        return view('admin.masters.customer_reward.add_customer_rewards.edit',compact('reward','pageTitle'));

    }

    public function editTempRewardToCustomer(Request $request,$reward_to_customer_temp_id)
    {
        $pageTitle = "Edit Reward To Customer";
        $reward_to_customer_temp_id  = Crypt::decryptString($reward_to_customer_temp_id);
        $dummy_reward = Trn_RewardToCustomerTemp::find($reward_to_customer_temp_id);

        return view('admin.masters.customer_reward.add_customer_rewards.edit_temp',compact('dummy_reward','pageTitle'));

    }

    public function updateRewardToCustomer(Request $request,$reward_to_customer_id)
    {
      try{ 

        $reward['user_id'] = auth()->user()->id;
        $reward['customer_mobile_number'] = $request->customer_mobile_number;
        $reward['reward_discription'] = $request->reward_discription;
        $reward['reward_points'] = $request->reward_points;
        $reward['added_date'] = Carbon::now()->format('Y-m-d');

        Mst_RewardToCustomer::where('reward_to_customer_id',$reward_to_customer_id)->update($reward);


        return redirect('admin/list/reward-to-customer')->with('status','Customer reward updated successfully');


        } catch (\Exception $e) {
             //return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
        
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }


    }

    public function updateTempRewardToCustomer(Request $request,$reward_to_customer_temp_id)
    {
      try{ 

        $reward['customer_mobile_number'] = $request->customer_mobile_number;
        $reward['reward_discription'] = $request->reward_discription;
        $reward['reward_points'] = $request->reward_points;
        $reward['added_date'] = Carbon::now()->format('Y-m-d');

        Trn_RewardToCustomerTemp::where('reward_to_customer_temp_id',$reward_to_customer_temp_id)->update($reward);

        return redirect('admin/list/reward-to-customer')->with('status','Customer reward updated successfully');


        } catch (\Exception $e) {
             return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
        
          //  return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }


    }

    public function removeRewardToCustomer(Request $request,$reward_to_customer_id)
    {
        Mst_RewardToCustomer::where('reward_to_customer_id',$reward_to_customer_id)->delete();

        return redirect()->back()->with('status','Deleted successfully.');
    
    }

    public function removeTempRewardToCustomer(Request $request,$reward_to_customer_temp_id)
    {
        Trn_RewardToCustomerTemp::where('reward_to_customer_temp_id',$reward_to_customer_temp_id)->delete();

        return redirect()->back()->with('status','Deleted successfully.');
    
    }

    



}
