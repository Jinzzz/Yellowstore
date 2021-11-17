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
                <strong>Whoops!</strong> 
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
         
               <div class="card-body">
                       
                <div class="table-responsive">
                  <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                      <tr>
                        <th class="wd-15p">S.No</th>
                        <th class="wd-15p">Product<br>Name</th>
                        <th class="wd-15p">Product<br>Category</th>
                        <th class="wd-15p">Current<br>Stock</th>

                       <th class="wd-15p">{{__('Action')}}</th>

                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $i = 0;
                      @endphp
                      @foreach ($products as $product)
                      <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{$product->product_name}}</td>
                        <td>{{$product->categories['category_name']}}</td>
                        <td id="td{{$product->product_id}}">{{$product->stock_count}}</td>
                        <td>
                          <form>
                            <input style="display:inline-block; width:70%;" type="number" id="stock_id{{$product->product_id}}" class="form-control"   placeholder="New Stock ">

                            <a onclick="updateStock({{$product->product_id}})" class="btn btn-icon btn-green"><i style="color:#ffffff;" class="fa fa-check" ></i></a>
                            <a onclick="resetStock({{$product->product_id}})" class="btn btn-icon btn-red"><i style="color:#ffffff;" class="fa fa-rotate-left"></i></a>
                         </form>
                           <span id="status_msg{{$product->product_id}}"></span>
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
    </div>
  </div>
  </div>

  <script>

  function resetStock(product_id)
  {
    $('#stock_id'+product_id).val('');
  }



  function updateStock(product_id)
  {
        var updated_stock = $('#stock_id'+product_id).val();
        var _token = $('input[name="_token"]').val();
          var current_stock =    $('#td'+product_id).text();

    $.ajax({
        url:"{{ route('store.stock_update') }}",
        method:"POST",
        data:{updated_stock:updated_stock,product_id:product_id, _token:_token},
        success:function(result)
        {
            if(result != "error"){
               // $('#status_msg'+product_id).html('<label class="text-success">Stock Updated</label>');
                $("#status_msg"+product_id).show().delay(1000).fadeOut();
                $("#stock_id"+product_id).val('');

                $('#td'+product_id).html(result);


              if(result > current_stock)
              {
                var $el = $("#td"+product_id),
                    x = 400,
                    originalColor = $el.css("background-color");

                $el.css("background", "#49e3428a");
                setTimeout(function(){
                  $el.css("background-color", originalColor);
                }, x);
              }
              else
              {
                var $el = $("#td"+product_id),
                    x = 400,
                    originalColor = $el.css("background-color");

                $el.css("background", "#d3202094");
                setTimeout(function(){
                  $el.css("background-color", originalColor);
                }, x);
              }

            }
            else
            {
                $("#stock_id"+product_id).val('');
               var $el = $("#td"+product_id),
                    x = 400,
                    originalColor = $el.css("background-color");

                $el.css("background", "#4871cc9c");
                setTimeout(function(){
                  $el.css("background-color", originalColor);
                }, x);
            }
        }
    });
}
  </script>

  @endsection
