<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    protected $table ="mst_towns";

    protected $primaryKey = "town_id";

    protected $fillable = [
                       'town_id',
                       'town_name',
                       'district_id',
                        ];


    public function district()
   {
   	return $this->belongsTo('App\Models\admin\District','district_id','district_id');
   }
}
