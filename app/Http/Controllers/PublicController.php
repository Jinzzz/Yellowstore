<?php

namespace App\Http\Controllers;



use App\Models\admin\Trn_store_customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Response;
use Image;
use DB;
use Hash;
use Carbon\Carbon;
use Crypt;
use Mail;
use PDF;

use Illuminate\Http\Request;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_store_order_item;

class PublicController extends Controller
{
    public function generatePdf(Request $request,$id)
    {
   
   
       $decrId  = Crypt::decryptString($id);
       $order = Trn_store_order::Find($decrId);
       $order_no = $order->order_number;
       $pageTitle = "Invoice";
       $order_items = Trn_store_order_item::where('order_id',$decrId)->get();
   
      // dd($order_no);
   
       $pdf = PDF::loadView('store.elements.order.bill', compact('order_items','order','pageTitle'));
   
     //return view('store.elements.order.bill',compact('order_items','pageTitle','order'));
   
   
       $content =  $pdf->download()->getOriginalContent();
   
       Storage::put('uploads\order_invoice\Ivoice_'.$order_no.'.pdf', $content);
   
       return $pdf->download('Ivoice_'.$order_no.'.pdf');
   
    }
}
