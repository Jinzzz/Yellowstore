@extends('store.layouts.app')
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12 col-lg-12">
      <div class="card">
        <div class="row">
          <div class="col-12">


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
                <form id="reset" method="GET" enctype="multipart/form-data">
                   @csrf
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Select Customer</label>
                                        <select name="customer_id" id="customer_id" class="form-control" >
                                             <option value="" >Select Customer</option>
                                             @foreach ($customer as $data)
                                                  <option value="{{ $data->customer_id }}" >{{ $data->customer_first_name }} {{ $data->customer_last_name }}</option>
                                             @endforeach
                                        </select>
                                 </div>
                            </div>

                             <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Select Product</label>
                                        <select name="product_id" id="product_id" class="form-control" >
                                             <option value="" >Select Product</option>
                                              @foreach ($products as $data)
                                                  <option value="{{ $data->product_id }}" >{{ $data->product_name }}</option>
                                             @endforeach
                                        </select>
                                 </div>
                            </div>
 
                              <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Quantity</label>
                                   <input type="number" onchange="changeQuantity()" class="form-control" id="quantity" name="quantity" value="{{request()->input('quantity')}}" placeholder="Quantity">
                                 </div>
                            </div>

                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Rate</label>
                                   <input type="text" readonly class="form-control" id="rate" name="rate" value="{{request()->input('rate')}}" placeholder="Rate">
                                 </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Discount %</label>
                                   <input type="number" onchange="changeDiscount()" value=0 class="form-control" id="discount" name="discount" value="{{request()->input('discount')}}" placeholder="Discount %">
                                 </div>
                            </div>

                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Total Discount</label>
                                   <input type="text" readonly class="form-control" id="total_discount" name="total_discount" value="{{request()->input('total_discount')}}" placeholder="Total Discount">
                                 </div>
                            </div>

                             <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Tax</label>
                                        <select onchange="changeTax()" name="tax_value" id="tax_value" class="form-control" >
                                             <option value="0">Tax</option>
                                             @foreach ($tax as $data)
                                                  <option value="{{ $data->tax_value }}" >{{ $data->tax_name }} - {{ $data->tax_value }}%</option>
                                             @endforeach
                                        </select>
                                 </div>
                            </div>

                             <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Amount</label>
                                   <input readonly type="text" class="form-control" id="total_amount" name="total_amount" value="{{request()->input('total_amount')}}" placeholder="Total Amount">
                                 </div>
                            </div>

                            <br>

                            <div class="col-md-12">
                                <div class="form-group">
                                <center>
                                   <a style="color:#ffffff;" onclick="submitProduct()" class="btn btn-block btn-blue"> Submit </a>
                                   {{-- <a style="color:#ffffff;" class="btn btn-block btn-blue"> Add More Product </a> --}}
                                </center>
                                </div>
                            </div>
                        </div>
                </form>
            </div>

               <div class="card-body">
                         
                <div class="table-responsive">
                  <form action="{{route('store.save_pos')}}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <table id="myTable" style="background: #f1f1f9;" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                      <tr>
                        <th class="wd-15p">Customer & Product</th>
                        <th class="wd-15p">Qty & Rate</th>
                        <th class="wd-15p">Discount %</th>
                        <th class="wd-15p">Tax</th>
                        <th class="wd-15p">Total</th>

                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $i = 0;
                      @endphp
     
                      
                    </tbody>
                  </table>
          <button id="order_btn" type="submit"  style="color:#ffffff;" class="btn btn-block btn-cyan"> Confirm Order </button>
       </form>

              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  </div>


     {{-- <div class="spinner1">
          <div class="double-bounce1"></div>
          <div class="double-bounce2"></div>
     </div> --}}
										

<script>
    $("#order_btn").hide();

          var customer_id = 0;
          var customer_name;
          var product_id = 0;
          var quantity = 0;
          var rate = 0;
          var discount = 0;
          var total_discount = 0;
          var tax_value = 0;
          var total_amount = 0;

          var product_name;
          var product_sale_price = 0;

     function changeQuantity()
     {  
           product_id = $('#product_id').val();
           quantity = $('#quantity').val();

          var _token = $('input[name="_token"]').val();
          
          $.ajax({
               url:"{{ route('store.find_product') }}",
               method:"POST",
               data:{product_id:product_id, _token:_token},
               success:function(result)
               {
                    product_name = result['product_name'];
                    product_sale_price = result['product_price_offer'];
                    $('#rate').val(quantity * product_sale_price);
                    $('#total_amount').val(quantity * product_sale_price); // remove it
                    changeDiscount();
               }
          })

        //  alert('cusId='+customer_id+'\n proid='+product_id+' \n qty='+quantity+'\n rate='+rate+' \n disc='+discount+'\n tDis='+total_discount+'\n taxId='+tax_id+'\n tAmt='+total_amount);
     }

     function changeDiscount()
     {
          discount = $('#discount').val();
          rate = $('#rate').val();
          total_discount = (discount / 100) * rate;
          total_amount = rate - total_discount;
          $('#total_discount').val( (discount / 100) * rate);
          $('#total_amount').val(total_amount); // remove it
     }

     function changeTax()
     {
          rate = $('#rate').val();
          total_discount = $('#total_discount').val();
          var total_rate = rate - total_discount;
          tax_value = $('#tax_value').val();
          tax = (parseFloat(tax_value) / 100) * total_rate;
          total_amount = parseFloat(total_rate) + parseFloat(tax);
          $('#total_amount').val(total_amount); // remove it
     }
     
     
     function submitProduct()
     {     
           customer_id = $('#customer_id').val();
           product_id = $('#product_id').val();
           quantity = $('#quantity').val();
           rate = $('#rate').val();
           discount = $('#discount').val();
           total_discount = $('#total_discount').val();
           tax_value = $('#tax_value').val();
           total_amount = $('#total_amount').val();

          var total_rate = rate - total_discount;
          var tax = (parseFloat(tax_value) / 100) * total_rate;


           if(customer_id != "" && product_id != "" && discount != ""  && rate != "" && total_amount != "")
           {
               var _token = $('input[name="_token"]').val();
               $.ajax({
                    url:"{{ route('store.find_customer') }}",
                    method:"POST",
                    data:{customer_id:customer_id, _token:_token},
                    success:function(result)
                    {
                         customer_name = result['customer_first_name']+' '+result['customer_last_name'];
                         //  alert(customer_name);
                         html = '<tr><td><input type="hidden" class=".classCustomerID" name="customer_id" value="'+customer_id+'">'+customer_name+' <br> <input type="hidden" class=".classProductID" name="product_id[]" value="'+product_id+'">'+product_name+' </td><td><input type="hidden" class=".classQuantity" name="quantity[]" value="'+quantity+'">'+quantity+' <i class="fa fa-times"></i> <br><input type="hidden" class=".classSingleQuantityRate" name="single_quantity_rate[]" value="'+(rate/ quantity)+'">'+(rate/ quantity)+'</td><td><input type="hidden" class=".classDiscountAmount" name="discount_amount[]" value="'+total_discount+'"><input type="hidden" class=".classDiscountPercentage" name="discount_percentage[]" value="'+discount+'">'+discount+'%</td><td><input type="hidden" class=".classTotalTax" name="total_tax[]" value="'+tax.toFixed(2)+'">'+tax.toFixed(2)+'</td><td class="price"><input type="hidden" class=".classTotalAmount" name="total_amount[]" value="'+parseFloat(total_amount).toFixed(2)+'">'+parseFloat(total_amount).toFixed(2)+'</td></tr>';                         
                         $('#myTable tr:last').after(html);
                         $('.total_sum').remove();
                              
                              $("#order_btn").show();
                              var total_sum = 0;
                              $(".price").each(function(){
                              total_sum += parseInt($(this).text());
                              $('.total_sum').remove(); 

                         });
                         // add total amount
                         html = '<tr class="total_sum"><td colspan="4" class=" text-right">Total</td><td class=""><input type="hidden" class=".classFullAmount" name="full_amount" value="'+total_sum+'">'+total_sum+'</td></tr>';
                         $('#myTable tr:last').after(html);


                    }
               })

                     $('#product_id').val('');
                     $('#quantity').val('');
                     $('#rate').val('');
                     $('#discount').val('');
                     $('#total_discount').val('');
                     $('#tax_value').val(0);
                     $('#total_amount').val('');

           }
           else
           {
                alert("Plese fill all fields..");
           }

         // alert('cusId='+customer_id+'\n proid='+product_id+' \n qty='+quantity+'\n rate='+rate+' \n disc='+discount+'\n tDis='+total_discount+'\n tax_value='+tax_value+'\n tAmt='+total_amount);

          


     }
     function saveOrder()
     {
            var  products 
               
               var _token = $('input[name="_token"]').val();
               $.ajax({
                    url:"{{ route('store.find_customer') }}",
                    method:"POST",
                    data:{customer_id:2, _token:_token},
                    success:function(result)
                    {
                        
                    }
               })
     }


</script>
  

  @endsection
