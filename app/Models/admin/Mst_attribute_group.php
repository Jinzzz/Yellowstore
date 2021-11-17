<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_attribute_group extends Model
{
    protected $primaryKey = "attr_group_id";

     protected $fillable = [
    					'attr_group_id','group_name',  
    										  ];

}
