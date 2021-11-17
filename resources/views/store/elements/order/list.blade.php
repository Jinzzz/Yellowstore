@extends('store.layouts.app')
@section('content')
<div class="container">
<div class="row justify-content-center">
<div class="col-md-12 col-lg-12">
<div class="card">
<div class="row">
   <div class="col-12" >

      @if ($message = Session::get('status'))
      <div class="alert alert-success">
         <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
      </div>
      @endif
      <div class="col-lg-12">
         @if ($errors->any())
         <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
               @foreach ($errors->all() as $error)
               <li>{{ $error }}</li>
               @endforeach
            </ul>
         </div>
         @endif
         <div class="card-header">
            <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
         </div>
         <div class="card-body border">
      <form action="{{route('store.list_order')}}" method="GET"
                enctype="multipart/form-data">
          @csrf
            <div class="row">

                <div class="col-md-6">
                <div class="form-group">
                     <label class="form-label"> Status</label>
                      <select class="form-control" name="status_id" id="status_id">
                                 <option value=""> Select Status</option>
                              @foreach ($status as $key)
                              <option {{request()->input('status_id') == $key->status_id ? 'selected':''}} value=" {{ $key->status_id}} "> {{ $key->status}}
                              </option>
                              @endforeach
                           </select>
                  </div>
               </div>
                <div class="col-md-6">
                <div class="form-group">

                       <label class="form-label">Delivery Boy </label>
                           <select name="delivery_boy_id" class="form-control" >
                                 <option value=""> Select Delivery Boy</option>
                                @foreach($delivery_boys as $key)
                                <option {{old('delivery_boy_id') == $key->delivery_boy_id ? 'selected':''}} value="{{$key->delivery_boy_id}}"> {{$key->delivery_boy_name }} </option>
                                @endforeach
                              </select>
                  </div>
               </div>


         <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">From Date</label>
                     <input type="date" class="form-control" name="date_from"  value="{{ request()->input('date_from') }}" placeholder="From Date">

                  </div>
               </div>
                 <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">To Date</label>
                     <input type="date" class="form-control" name="date_to" value="{{ request()->input('date_to') }}" placeholder="To Date">

                  </div>
               </div>
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           <button type="reset" class="btn btn-raised btn-success">Reset</button>
                          <a href="{{route('store.list_order')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
    </div>
       </form>
    </div>
 <div class="card-body">

            <div class="table-responsive">
               <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                  <thead>
                     <tr>
                        <th class="wd-15p">SL.No</th>
                         <th class="wd-15p">Order<br>Number</th>
                        <th class="wd-15p">Customer<br>Name</th>
                        <th class="wd-15p">Order<br>Date</th>
                        <th class="wd-15p">Total<br>Order Amount</th>
                        <th class="wd-15p">{{__('Status')}}</th>
                        <th class="wd-15p">{{__('Action')}}</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $i = 0;
                     @endphp
                     @foreach ($orders as $order)
                     <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $order->order_number}}</td>
                        <td>
                           {{ @$order->customer->customer_first_name}}
                           {{ @$order->customer->customer_last_name}}
                        </td>

                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y')}}</td>
                        <td>{{ $order->product_total_amount}}</td>

                        <td>

                            <button type="button" data-toggle="modal" data-target="#StockModal{{$order->order_id}}"  class="btn btn-sm
                                @if($order->status_id == 1) btn-info @elseif($order->status_id == 5) btn-danger @else btn-success @endif">
                                @if($order->status_id == 1)Pending
                                @elseif($order->status_id == 2)Payment Successful
                                @elseif($order->status_id == 3)Payment Cancelled
                                @elseif($order->status_id == 4)Confirmed
                                @elseif($order->status_id == 5)Cancelled
                                @elseif($order->status_id == 6)Completed
                                @elseif($order->status_id == 7)Shipped
                                @elseif($order->status_id == 8)Out For Delivery

                                @else Delivered
                          @endif</button>

                         </td>

                          <td>
                       <a class="btn btn-sm btn-blue"  href="{{url('store/order/view/'.Crypt::encryptString($order->order_id))}}">View</a>
                        {{-- <a class="btn btn-sm btn-info"  href="{{url('store/assign_order/delivery_boy/'.Crypt::encryptString($order->order_id))}}">Assign Order</a> --}}
                        
                          {{--  <a href="{{url('store/product_invoice/pdf/'.$order->order_id)}}" class="btn btn-info btn-sm">Generate Invoice</a> --}}
                       <a class="btn btn-sm btn-indigo"
                        href="{{url('store/order/invoice/'.Crypt::encryptString($order->order_id))}}">Invocie</a>
                         <a class="btn btn-sm btn-info" href="{{url('store/product_invoice/pdf/'.Crypt::encryptString($order->order_id))}}">Generate Invoice </a>
                    @php
                    $url = url('get/invoice/'.Crypt::encryptString($order->order_id));
                    $cus_name = $order->customer->customer_first_name;
                      $msg = 'Hi '.$cus_name.' your invoice is ready.     '.$url;
                   //  $msg = nl2br("hi $cus_name\r\n$url");
   //  echo $msg;die;
                    @endphp
<br>
<br>

                      <a class="btn btn-sm btn-success" target="_blank" href="https://api.whatsapp.com/send?phone=+91{{$order->customer->customer_mobile_number}}&text={!!$msg!!}" data-action="share/whatsapp/share">Share Invoice On Whatsapp</a>

                       {{-- <a class="btn btn-sm btn-indigo"  href="{{url('store/product_invoice/whatsup/send/'.Crypt::encryptString($order->order_id))}}">Send to Whatsup</a> --}}

                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
 @foreach($orders as $order)
            <div class="modal fade" id="StockModal{{$order->order_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action="{{ route('store.order_status',$order->order_id) }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">
                    <input type="hidden" class="form-control" name="order_id" value="{{$order->order_id}}" >

                   <label class="form-label"> Status</label>
                      <select class="form-control" name="status_id" id="status_id">
                                 <option value=""> Select Status</option>
                              @foreach ($status as $key)
                              <option {{request()->input('status_id',$order->status_id) == $key->status_id ? 'selected':''}} value=" {{ $key->status_id}} "> {{ $key->status}}
                              </option>
                              @endforeach
                           </select>
                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>
            @endforeach
<!-- MESSAGE MODAL CLOSED -->
@endsection
