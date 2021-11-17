@extends('admin.layouts.app')
@section('content')
<div class="container">
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
            </div>
            <div class="card-body">
               @if ($message = Session::get('status'))
               <div class="alert alert-success">
                  <p>{{ $message }}</p>
               </div>
               @endif
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
                                    <tbody><tr class=" ">
                                       <th class="text-center">Si. No</th>
                                       <th>Product</th>
                                       <th class="text-center">Quantity</th>
                                       @if (@$order->store->store_doc['store_document_gstin'])
                                       <th>GST</th>
                                       @endif
                                       <th class="text-right">Unit Price</th>
                                       <th class="text-right">Tax Amount (12%)</th>
                                       <th class="text-right">Sub Total</th>
                                    </tr>
                                    <tr>
                                       <td class="text-center">1</td>

                                       <td>
                                          <p class="font-w600 mb-1">{{@$order->product->product['product_name']}}</p>
                                          <div class="text-muted"><div class="text-muted"></div></div>
                                       </td>
                                       <td class="text-center">{{@$order->quantity}}</td>
                                       @if (@$order->store->store_doc['store_document_gstin'])
                                       <td>
                                       {{@$order->store->store_doc['store_document_gstin']}}
                                       </td>
                                       @endif
                                       <td class="text-right">{{@$order->product->product['product_price']}}</td>
                                       <td class="text-right">{{ ((12/100) * ((@$order->quantity) * (@$order->product->product['product_price']))) }}</td>
                                       <td class="text-right">{{(($order->quantity) * (@$order->product->product['product_price'])) + ((12/100) * ((@$order->quantity) * (@$order->product->product['product_price'])))}}</td>
                                    </tr>

                                    <tr>
                                       <td colspan="
                                       @if (@$order->store->store_doc['store_document_gstin'])
                                       6
                                       @else
                                       5
                                       @endif
                                       " class=" text-right">Delivery Charge</td>
                                       <td class=" text-right h4">{{ @$order->delivery_charge}}</td>
                                    </tr>
                                        <tr>
                                       <td colspan="
                                       @if (@$order->store->store_doc['store_document_gstin'])
                                       6
                                       @else
                                       5
                                       @endif
                                       " class=" text-right">Packing Charge</td>
                                       <td class=" text-right h4"> 20 </td>
                                    </tr>

                                     <tr>
                                       <td colspan="
                                       @if (@$order->store->store_doc['store_document_gstin'])
                                       6
                                       @else
                                       5
                                       @endif
                                       " class=" text-right">Discount</td>
                                       <td class=" text-right h4">10</td>
                                    </tr>
                                    <tr>
                                       <td colspan="
                                        @if (@$order->store->store_doc['store_document_gstin'])
                                       6
                                       @else
                                       5
                                       @endif" class="font-weight-bold text-uppercase text-right">Total</td>
                                       <td class="font-weight-bold text-right h4">INR {{ ( ((@$order->quantity) * (@$order->product->product['product_price'])) + ((12/100) * ((@$order->quantity) * (@$order->product->product['product_price']))) ) + 20 +  @$order->delivery_charge - 10 }}</td>
                                    </tr>
                                 </tbody></table>
                              </div>
                              <br>
                           </div>
                        </div>
                     </div>
                  </div>
                        <div class="card-footer text-right">

                              <button type="button" id="print" class="btn btn-info mb-1" onClick="printdiv('div_print');"><i class="si si-printer"></i> Print Invoice</button>
                     <button type="button" class="btn btn-cyan mb-1" onclick="history.back()">Cancel</button>

                     </div><!-- COL-END -->

          </div>
            </div>
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
