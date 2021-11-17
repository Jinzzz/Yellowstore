@extends('admin.layouts.app')
@section('content')
<style>
.exam{
     text-align:left;
  width:100%;
}



iframe{
  width: 40% ! important;
  height: 40% ! important;
}
</style>
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
                        <h5>Whoops!</h5> There were some problems with your input.<br><br>
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
                    {{-- <div class="card-body border">

                </div> --}}

                    <div class="card-body">
                        <a href="  {{route('admin.create_video')}} " class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create Video
                        </a>
                        </br>
                        <div class="table-responsive">
                           <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Platform') }}</th>
                                    <th class="wd-15p">{{ __('Video') }}</th>
                                    <th class="wd-15p">{{ __('Status') }}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($video as $value)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $value->platform}}</td>
                                    @php
                                       $vid = $value->video_code;
                                    @endphp
                                    <td  >
                                    <div class="exam"> {!!$value->video_code!!} </div>
                                    </td>
                                      <td> 
                                       @if ($value->status == 1)
                                          Active
                                       @else
                                          InActive
                                       @endif
                                      </td>

                                    <td>
                                       <form action="{{route('admin.destroy_video',$value->video_id)}}" method="POST">
                                         @csrf
                                          @method('POST')
                                          <a class="btn btn-sm btn-cyan"  href="{{url('admin/video/edit/'.
                                          Crypt::encryptString($value->video_id))}}">Edit</a>
                                          <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                       </form>
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

            <!-- MESSAGE MODAL CLOSED -->
            @endsection
