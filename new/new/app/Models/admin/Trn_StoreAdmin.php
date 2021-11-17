<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_StoreAdmin extends Model
{
    protected $primaryKey = "store_admin_id";
    protected $table = "trn__store_admins";

    protected $fillable = [
        'store_id','admin_name','email','username',
        'phone','role_id','status','password',
                        ];
                                                   
    public function store()
   {
   	return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
   }
}
