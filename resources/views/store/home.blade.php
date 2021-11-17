@extends('store.layouts.app')
@section('content')
 @php
//use App\Models\admin\category\Category;
//use App\Models\admin\insurance\Insurance;
//use App\Models\admin\template\Template;
//use App\Models\admin\job_seeker\TrnJobSeeker;
use App\Models\admin\Mst_StoreAppBanner;
use App\Models\admin\Mst_categories;
        $banners = Mst_StoreAppBanner::where('town_id',auth()->user()->town_id)->get();



@endphp
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<!-- ROW-1 -->
@if(count($banners) > 0)

<div class="row" >
<div class="pb-5 col-lg-12 col-md-12 col-sm-12 col-xl-12">




        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @foreach ($banners as $data)
                    <li data-target="#carouselExampleIndicators" data-slide-to="{{$loop->iteration}}" class="@if($loop->iteration == 1) active @endif"></li>
                @endforeach
            </ol>
            <div class="carousel-inner">
                @foreach ($banners as $data)
                        <div class="carousel-item @if($loop->iteration == 1) active @endif">
                        <img class=" w-70" src="{{asset('assets/uploads/store_banner/'.$data->image)}}" >
                        </div>
                @endforeach
            </div>
            {{-- <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a> --}}
            </div>
</div>
@endif

   <div class="col-lg-12 col-md-12 col-sm-12 col-xl-6">
      <div class="row">




        <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="#">
                    <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-info mb-0 box-info-shadow">
													<i class="fa fa-sliders text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Categories') }}</h6>
                        <h2 class="mb-2 number-font">{{ Mst_categories::count() }}</h2>
                        <p class="text-muted">{{ __('Total Categories ') }}</p>
                    </div>
               </a>
            </div>
        </div>


         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('store.list_product') }}">
                    <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-primary mb-0 box-primary-shadow">
                            <i class="fa fa-product-hunt text-white"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{ __('Products') }}</h6>
                        <h2 class="mb-2 number-font">{{$product}}</h2>
                        <p class="text-muted">{{ __('Registered Products ') }}</p>
                    </div>
               </a>
            </div>
        </div>







           <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="#">
                    <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-info mb-0 box-info-shadow">
													<i class="fa fa-truck text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Delivery Boys') }}</h6>
                        <h2 class="mb-2 number-font">{{ @$delivery_boys }}</h2>
                        <p class="text-muted">{{ __('Total Delivery Boys ') }}</p>
                    </div>
               </a>
            </div>
        </div>

          <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="#">
                    <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-primary mb-0 box-info-shadow">
													<i class="fa fa-comments text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Issues') }}</h6>
                        <h2 class="mb-2 number-font">{{ @$dispute }}</h2>
                        <p class="text-muted">{{ __('Total Issues ') }}</p>
                    </div>
               </a>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="#">
                    <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-warning mb-0 box-info-shadow">
													<i class="fa fa-comments text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Current Issues') }}</h6>
                        <h2 class="mb-2 number-font">{{ @$dispute_current }}</h2>
                        <p class="text-muted">{{ __('Total Current Issues ') }}</p>
                    </div>
               </a>
            </div>
        </div>

         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="#">
                    <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-danger mb-0 box-info-shadow">
													<i class="fa fa-comments text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Current Issues') }}</h6>
                        <h2 class="mb-2 number-font">{{ @$dispute_new }}</h2>
                        <p class="text-muted">{{ __('Total Current Issues ') }}</p>
                    </div>
               </a>
            </div>
        </div>






      </div>

   </div> 


   <!-- COL END -->
   <div class="col-lg-12 col-md-12 col-sm-12 col-xl-6">






 <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('store.list_order') }}">
                  <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-secondary mb-0 box-secondary-shadow">
                     <i class="fe fe-codepen text-white"></i>
                  </div>
                  <h6 class="mt-4 mb-1">{{ __('Orders') }}</h6>
                  <h2 class="mb-2 number-font">{{$order}}</h2>
                  <p class="text-muted">{{ __('Total Orders') }}</p>
               </div>
               </a>
            </div>
         </div>


  <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="#">
                    <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-info mb-0 box-info-shadow">
													<i class="fe fe-trending-up text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Total Sales') }}</h6>
                        <h2 class="mb-2 number-font">	<i class="fa fa-rupee"></i> {{ @$total_sale }}</h2>
                        <p class="text-muted">{{ __('Total Categories ') }}</p>
                    </div>
               </a>
            </div>
        </div>


         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="#">
                    <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-warning mb-0 box-info-shadow">
													<i class="fe fe-trending-up text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Today\'s Sales') }}</h6>
                        <h2 class="mb-2 number-font">	<i class="fa fa-rupee"></i> {{ @$today_sale }}</h2>
                        <p class="text-muted">{{ __('Total Categories ') }}</p>
                    </div>
               </a>
            </div>
        </div>

         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="#">
                    <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-cyan mb-0 box-info-shadow">
													<i class="fa fa-calendar text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Daily Sales') }}</h6>
                        <h2 class="mb-2 number-font">	<i class="fa fa-rupee"></i> {{ @$today_sale_count }}</h2>
                        <p class="text-muted">{{ __('Total Daily Sales ') }}</p>
                    </div>
               </a>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('store.list_agency') }}">
                  <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-success mb-0 box-success-shadow">
                     <i class="fe fe-aperture text-white"></i>
                  </div>
                  <h6 class="mt-4 mb-1">{{ __('Agency') }}</h6>
                  <h2 class="mb-2  number-font">{{$agency}}</h2>
                  <p class="text-muted">{{ __('Total Agency') }}</p>
               </div>
               </a>
            </div>
         </div>


    </div>





</div>
</div>

<!-- ROW-1 END -->
</div>

</div>
<!-- CONTAINER END -->
</div>
@endsection
