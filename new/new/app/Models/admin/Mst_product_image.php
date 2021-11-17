<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_product_image extends Model
{
    protected $primaryKey = "product_image_id";

    protected $fillable = ['product_image_id', 'product_image','image_flag',

    						'product_varient_id','product_id'
							
							];

}
