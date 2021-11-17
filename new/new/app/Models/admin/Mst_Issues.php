<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_Issues extends Model
{
    protected $table ="mst__issues";

    protected $primaryKey = "issue_id";


    protected $fillable = [
    					    'issue',
    						  ];
}
