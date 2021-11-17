@extends('store.layouts.app')
@section('content')

<style>
input[type="file"] {
  display: block;
}
.imageThumb {
  max-height: 75px;
  border: 2px solid;
  padding: 1px;
  cursor: pointer;
}
.pip {
  display: inline-block;
  margin: 10px 10px 0 0;
}
.remove {
  display: block;
  background: #444;
  border: 1px solid black;
  color: white;
  text-align: center;
  cursor: pointer;
}
.remove:hover {
  background: white;
  color: black;
}
</style>

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

               <form action="{{route('store.store_product')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Product Name *</label>
                           <input type="text" class="form-control" required
                              name="product_name" value="{{old('product_name')}}" placeholder="Product Name">
                        </div>
                     </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" >Product Description *</label>
                            <textarea class="form-control" id="product_description" required
                                name="product_description" rows="2" cols="3" placeholder="Product Description">{{old('product_description')}}</textarea>
                        </div>
                     </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Regular Price *</label>
                            <input type="text" class="form-control" required onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="regular_price"   id="regular_price" value="{{old('regular_price')}}" placeholder="Regular Price">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Sale Price *</label>
                            <input type="text" class="form-control" required onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="sale_price"  id="sale_price" value="{{old('sale_price')}}" placeholder="Sale Price">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Tax *</label>
                           <select required  name="tax_id" id="tax_id" class="form-control"  >
                                 <option value="">Tax</option>
                                @foreach($tax as $key)
                                <option {{old('tax_id') == $key->tax_id ? 'selected':''}} value="{{$key->tax_id }}"> {{$key->tax_name }} ( {{$key->tax_value}} ) </option>
                                @endforeach
                              </select>
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Min Stock *</label>
                           <select required name="min_stock" id="min_stock" class="form-control"  >
                                 <option value="">Min Stock</option>
                                 @for ($i = 0; $i < 21; $i++)
                                    <option {{old('min_stock') == $i ? 'selected':''}}  value="{{$i}}">{{$i}}</option>
                                 @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Product Code *</label>
                            <input type="text" required class="form-control" name="product_code" id="product_code" value="{{old('product_code')}}" placeholder="Product Code">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Product Type *</label>
                           <select name="business_type_id" required id="business_type" class="form-control"  >
                                 <option value=""> Select Business Type</option>
                                @foreach($business_types as $key)
                                <option {{old('business_type_id') == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id }}"> {{$key->business_type_name }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div>

                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="form-label">Color *</label>
                           <select name="color_id" required id="color_id" class="form-control"  >
                                 <option value="">Color</option>
                                @foreach($colors as $key)
                                <option {{old('color_id') == $key->attr_value_id ? 'selected':''}} value="{{$key->attr_value_id }}"> {{$key->group_value }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div>

                    <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Attribute *</label>
                       <select name="attr_group_id" required class="attr_group form-control" >
                         <option value="">Attribute</option>
                          @foreach($attr_groups as $key)
                          <option {{old('attr_group_id') == $key->attr_group_id ? 'selected':''}} value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>
                                @endforeach
                              </select>
                     </div>
                   </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Value *</label>
                            <select name="attr_value_id" required  class="attr_value form-control" >
                            <option value="">Value</option>
                            @if( old('attr_value_id'))
                              @php
                                $att_value = DB::table('mst_attribute_values')->where('attr_value_id',old('attr_value_id'))->first();
                              @endphp
                              <option selected value="{{  @$att_value->attr_value_id}}">{{ @$att_value->group_value }}</option>
                            @endif
                                </select>
                        </div>
                    </div>

                     <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" >Product Category * </label>
                            <select name="product_cat_id" required id="category" class="form-control"  >
                                 <option value="">Product Category</option>
                                 @if( old('product_cat_id'))
                              @php
                                $category = DB::table('mst_store_categories')->where('category_id',old('product_cat_id'))->first();
                              @endphp
                              <option selected value="{{ @$category->category_id }}">{{ @$category->category_name }}</option>
                            @endif       

                            </select>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Vendor *</label>
                           <select required name="vendor_id" id="vendor_id" class="form-control"  >
                                 <option value="">Vendor</option>
                                @foreach($agencies as $key)
                                <option {{old('vendor_id') == $key->agency_id ? 'selected':''}} value="{{$key->agency_id }}"> {{$key->agency_name }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div>

                <div class="col-md-12">
                   <div class="form-group">
                    <div class="BaseFeatureArea">
                        {{-- <label class="form-label">Upload Images(1000*800) *</label> --}}
                        <label class="form-label">Upload Images *</label>
                        <input type="file" accept="image/*" required class="form-control" name="product_image[]" multiple=""  placeholder="Product Feature Image">
                        <br>
                     </div>
                     </div>
                 </div>


                  </div>
                <br>
              <div class="row">
                      <div class="col-md-12">
                     <div class="form-group">
                     <center>
                            <button type="submit" class="btn btn-raised btn-info">
                           Submit</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('store.list_product') }}">Cancel</a>
                       </center>
                     </div>
                  </div>
                </div>
       </form>
            <script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script>
            <script>CKEDITOR.replace('product_description');</script>
            <script>//CKEDITOR.replace('product_specification');</script>
             <script>//CKEDITOR.replace('product_delivery_info');</script>
             <script>//CKEDITOR.replace('product_delivery_info');</script>
            <script>//CKEDITOR.replace('product_shipping_info');</script>


      </div>
   </div>
</div>
</div>
@endsection

{{-- .varient {
  background-color: #EDEDFD;
  border: 1px  grey;
} --}}

 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>

<script>
$(document).ready(function() {
  if (window.File && window.FileList && window.FileReader) {
    $("#files").on("change", function(e) {
      var files = e.target.files,
        filesLength = files.length;
      for (var i = 0; i < filesLength; i++) {
        var f = files[i]
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {
          var file = e.target;
          $("<span class=\"pip\">" +
            "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
            "<br/><span class=\"remove\">Remove image</span>" +
            "</span>").insertAfter("#files");
          $(".remove").click(function(){
            $(this).parent(".pip").remove();
          });

          // Old code here
          /*$("<img></img>", {
            class: "imageThumb",
            src: e.target.result,
            title: file.name + " | Click to remove"
          }).insertAfter("#files").click(function(){$(this).remove();});*/

        });
        fileReader.readAsDataURL(f);
      }
      console.log(files);
    });
  } else {
    alert("Your browser doesn't support to File API")
  }
});
</script>


<script type="text/javascript">


$(document).ready(function() {
   var wrapper      = $(".BaseFeatureArea"); //Fields wrapper
  var add_button      = $(".addBaseFeatureImage"); //Add button ID

  var x = 1; //initlal text box count
  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div>  <input type="file" class="form-control" name="product_image[]"  value="{{old('product_image')}}" placeholder="Base Product Feature Image" /> <a href="#" class="remove_field btn btn-small btn-danger">Remove</a></div>'); //add input box

  });

  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});




 $(document).ready(function() {

var agc = 0;

       $('.attr_group').change(function(){
       // alert("hi");
       if(agc != 0)
       { 
       // alert("dd");
        var attr_group_id = $(this).val();

        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('store/product/ajax/get_attr_value') }}?attr_group_id="+attr_group_id,


          success:function(res){
            //alert(data);
            if(res){
            $('.attr_value').prop("diabled",false);
            $('.attr_value').empty();
            $('.attr_value').append('<option value="">Value</option>');
            $.each(res,function(attr_value_id,group_value)
            {
              $('.attr_value').append('<option value="'+attr_value_id+'">'+group_value+'</option>');
            });

            }else
            {
              $('.attr_value').empty();

            }
            }

        });
       }
       else
       {
         agc++;
       }
      });

    });
  $(document).ready(function() {
    var pcc = 0;
      //  alert("dd");
       $('#business_type').change(function(){
         if(pcc != 0)
         { 
        var business_type_id = $(this).val();
       //alert(business_type_id);
        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('store/product/ajax/get_category') }}?business_type_id="+business_type_id,


          success:function(res){
           // alert(data);
            if(res){
            $('#category').prop("diabled",false);
            $('#category').empty();
            $('#category').append('<option value="">Product Category</option>');
            $.each(res,function(category_id,category_name)
            {
              $('#category').append('<option value="'+category_id+'">'+category_name+'</option>');
            });

            }else
            {
              $('#category').empty();

            }
            }

        });
         }else{
           pcc++;
         }
      });

    });

</script>
