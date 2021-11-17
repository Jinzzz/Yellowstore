@extends('admin.layouts.app')
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
                        <div class="card-body">
                                    <a  data-toggle="modal" data-target="#StockModal01" class="btn btn-block btn-info">
                                    <i class="fa fa-plus"></i> Add Tax </a>
                                <br>
                            <div class="table-responsive">
                            <table id="example" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('Tax Name')}}</th>
                                        <th class="wd-15p">{{__('Tax Value')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($taxes as $tax)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $tax->tax_name}}</td>
                                        <td>{{ $tax->tax_value}}</td>

                                        <td>
                                            <form action="{{route('admin.destroy_tax',$tax->tax_id)}}" method="POST">
                                                @csrf
                                                    <a class="btn btn-sm btn-cyan"  data-toggle="modal" data-target="#StockModal{{$tax->tax_id}}"
                                            >Edit</a>
                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>

                            </table>

                            {{-- table responsive end --}}
                            </div>
                        {{-- Card body end --}}
                        </div>
                    {{-- col 12 end --}}
                </div>
            {{-- row end --}}
            </div>
        {{-- card --}}



        </div>
        {{-- row justify end --}}
    </div>
{{-- container end --}}
</div>


      <div class="modal fade" id="StockModal01" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Add Tax</h5>
                        <button type="button" class="close" data-dismiss="modal"  onclick="clearTax()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.create_tax') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">

                   <label class="form-label">Tax Name</label>
                    <input type="text" required  id="tax_name"  class="form-control" value="" placeholder="Tax Name" name="tax_name"  >


                     <label class="form-label">Tax Value</label>
                    <input type="number" id="tax_value" required class="form-control" onchange="if (this.value < 0) this.value = '';" placeholder="Tax Value" name="tax_value" >

  	{{-- <label class="custom-switch">
                                                        <input type="hidden" name="isActive" value=0 />
														<input type="checkbox" name="isActive"  checked value=1 class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description">Active Status</span>
													</label> --}}
                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Add</button>
                        <button type="button" class="btn btn-secondary" onclick="clearTax()" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>



@foreach ($taxes as $tax)

              <div class="modal fade" id="StockModal{{$tax->tax_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Edit Tax</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.update_tax',$tax->tax_id) }}" method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">
                     <label class="form-label">Tax Name</label>
                    <input type="text" required class="form-control" value="{{@$tax->tax_name}}" placeholder="Tax Name" name="tax_name"  >

                    <label class="form-label">Tax Value</label>
                    <input type="number" required class="form-control" value="{{$tax->tax_value}}" onchange="if (this.value < 0) this.value = '';" placeholder="Tax Value" name="tax_value"  >


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
<!-- MESSAGE MODAL CLOSED -->

<script>
function clearTax()
{
      $('#tax_value').val('');
      $('#tax_name').val('');

}
</script>
                                    @endforeach

@endsection
