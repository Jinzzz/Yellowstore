@extends('store.layouts.app')
@section('content')
<div class="container">
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
            </div>
            <div class="card-body">
              
            </div>
            <div class="col-md-4">
            {{--  <a href="{{route('store.generate_invoice_pdf')}}" class="btn btn-info ">Generate Invoice</a> --}}
          </div>
            <div id="div_print"><br><br><br><br>
            <div class="col-lg-12">
              
               
                 <input type="hidden" class="form-control" name="order_id" value="{{$order->order_id}}">
                 
                 <div class="col-md-12">
                 <div class="row">
                                <div class="col-md-6 text-left">
                                    <p class="h3">Invoice From:</p>
                                      @php
                                        $invoice_data = \DB::table('trn_order_invoices')->where('order_id',$order->order_id)->first();
                                       @endphp
                                    <p class="h4">Invoice Number : {{@$invoice_data->invoice_id}}</p>
                                    <p class="h4">Invoice Date : {{$changeDate = date("d-m-Y", strtotime( @$invoice_data->invoice_date))  }}</p>


                                    <address>
                                    <h5>
                                    Yelowstore Home Infra solutions PVT Ltd<br>
                                    G1 - AVI Building 15/2 N.M Street<br>
                                    Krishnapuram,<br>
                                    Chennai - 600053<br>
                                    care@yellowstore.in</h5>
                                    </address>
                                 </div>

                                 <div class="col-md-6 text-right">
                                    <p class="h3">Invoice To:</p>
                                    <address>
                                    <h5>
                                   <p class="h4"> {{@$order->customer['customer_first_name']}} {{@$order->customer['customer_last_name']}} </p>
                                   {{@$order->customer['customer_mobile_number']}}<br>
                                   {{@$order->customer['customer_address']}}<br>
                                    Pincode : {{$order->customer['customer_pincode']}}<br>
                                    {{@$order->customer['customer_email']}}</h5>
                                    </address>
                                 </div>
                              </div>
                  </div>
              
            <br>
                  <div class="col-md-12">
                  <div class="table-responsive push">
                        <table class="table table-bordered table-hover mb-0 text-nowrap">
                          <thead>
                                    <tr>
                                       <td>Item Name</td>
                                       <td>Qty</td>
                                       <td>Rate</td>
                                       <td>Discount Amount</td>
                                       <td>Tax</td>
                                       <td>Total</td>
                                    </tr>
                           </thead>
                           <tbody>  
                                    @php
                                       $dis_amt = 0;
                                    @endphp
                                    @foreach ($order_items as $order_item)
                                       <tr>
                                          <td>{{@$order_item->product->product_name}}    </td>
                                          <td>{{@$order_item->quantity}} </td>
                                          <td>{{@$order_item->unit_price}} </td>
                                          <td>{{@$order_item->discount_amount}} </td>
                                          <td>{{@$order_item->tax_amount}} </td>
                                          <td>{{@$order_item->total_amount}} </td>
                                          
                                       </tr>
                                       @php
                                          $dis_amt =  $dis_amt + @$order_item->discount_amount;
                                       @endphp
                                    @endforeach
                                    @if(@$order->payment_type == '1')
                                    <tr>
                                       <td colspan="5" class=" text-right">Delivery Charge</td>
                                       <td class="  h4">{{ @$order->delivery_charge}}</td>
                                    </tr>
                                    @endif
                                    
                                    <tr>
                                       <td colspan="5" class=" text-right">Packing Charge</td>
                                       <td class=" h4"> 20 </td>
                                    </tr>

                                     <tr>
                                       <td colspan="5" class=" text-right">Total Discount</td>
                                       <td class=" h4"> {{ @$dis_amt }}</td>
                                    </tr>
                                    <tr>
                                       <td colspan="5" class="font-weight-bold text-uppercase text-right">Total</td>
                                       <td class="font-weight-bold  h4"><i class="fa fa-inr"></i> {{ @$order->product_total_amount + 20 }}</td>
                                    </tr>
                                 </tbody>

                        </table>
                              </div>
                              <br>
                           </div>
                        </div>
                     </div>

                        <div class="card-footer text-right">
                                <a href="{{route('store.list_order')}}" class="btn btn-cyan mb-1"  >Cancel</a>
                                

                              <button type="button" class="btn btn-info mb-1"  onClick="printdiv('div_print');"><i class="si si-printer"></i> Print Invoice</button>
                         
                     </div><!-- COL-END -->
                 
             
         </div>
      </div>
   </div>

   <script type="text/javascript">
        function printdiv(printpage) {
            var headstr = "<html><head><title></title></head><body>";
            var footstr = "</body>";
            var newstr = document.all.item(printpage).innerHTML;
            var oldstr = document.body.innerHTML;
            document.body.innerHTML = headstr + newstr + footstr;
            window.print();
            location.reload();
            return false;
        }
    </script>

   @endsection
  
   