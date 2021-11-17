<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_Video extends Model
{
    protected $primaryKey = "video_id";
    protected $table = "mst__videos";

   protected $fillable = [
                           'platform','video_code',
                           'status',
               ];
}
