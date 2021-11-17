<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_configure_points extends Model
{
    protected $primaryKey = "configure_points_id";
    protected $table = "trn_configure_points";

   protected $fillable = [
                           'points','order_amount',
                           'valid_from','isActive',
               ];



}
