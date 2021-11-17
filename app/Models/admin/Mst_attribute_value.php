<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\softDeletes;

class Mst_attribute_value extends Model
{
     protected $primaryKey = "attr_value_id";
     //use softDeletes;

     protected $fillable = [
    					'attr_value_id','group_value','attribute_group_id',				
    						  ];
public function attr_group()
   {
   	return $this->belongsTo('App\Models\admin\Mst_attribute_group','attribute_group_id','attr_group_id');
   }
}
