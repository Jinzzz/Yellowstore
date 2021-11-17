<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_StoreAppBanner extends Model
{
    protected $table ="mst__store_app_banners";
	protected $primaryKey = "banner_id";

    protected $fillable = [
    					'banner_id','image','town_id'
    					  ];

    public function town() //town  relation
   {
   	return $this->belongsTo('App\Models\admin\Town','town_id','town_id');
   }
}
