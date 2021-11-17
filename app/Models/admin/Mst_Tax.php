<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_Tax extends Model
{
    protected $primaryKey = "tax_id";
    protected $table = "mst__taxes";

   protected $fillable = [
                           'tax_value','tax_name'
               ];
}
