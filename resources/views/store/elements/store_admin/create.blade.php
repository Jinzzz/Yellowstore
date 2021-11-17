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
               <form action="{{route('store.store_store_admin')}}" method="POST"  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Name *</label>
                                <input type="text" required class="form-control" name="admin_name" value="{{old('admin_name')}}" placeholder="Name">
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Phone *</label>
                                <input type="text" required class="form-control" name="phone" value="{{old('phone')}}" placeholder="Name">
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Email</label>
                                <input type="email" class="form-control" name="email" value="{{old('email')}}" placeholder="Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label class="form-label">Username *</label>
                                <input type="text" required=""  name="username" class="form-control"  value="{{old('username')}}" placeholder="Username">
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label class="form-label">Role *</label>
                                 <select required=""  name="role_id"  class="form-control"  >
                                    <option  value="">Role</option>
                                    <option  {{old('role_id') == '2' ? 'selected':''}} value="2">Admin</option>
                                    <option {{old('role_id') == '3' ? 'selected':''}} value="3">Manager</option>
                                    <option {{old('role_id') == '4' ? 'selected':''}} value="4">Staff</option>
                                 </select>
                              </div>
                        </div>

               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Password *</label>
                          <input type="Password" required="" name="password" class="form-control" placeholder=" Password" value="{{old('password')}}">

                  </div>

                  </div>
                   <div class="col-md-6">
                  <div class="form-group">

                    <label class="form-label">Confirm Password *</label>
                    <input type="password"  class="form-control"
                    name="password_confirmation"  placeholder="Confirm Password">

                  </div>
                  </div>
                                    <div class="col-md-2">
                                   <br> <br>
                                	<label class="custom-switch">
                                                        <input type="hidden" name="status" value=0 />
														<input type="checkbox" name="status"  checked value=1 class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description">Active Status</span>
													</label>
                            </div>
                  </div>
                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('store.store_admin') }}">Cancel</a>
                           </center>
                        </div>
               </form>

         </div>
      </div>
   </div>
</div>

 @endsection
