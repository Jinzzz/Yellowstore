<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
  <div class="side-header" style="background-color: #9e9e9e;">
    <a class="header-brand1 " style="display: flex;align-items: center;" href="{{route('home')}}">
    <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img desktop-logo " alt="logo">
      <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img toggle-logo" alt="logo">
      <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img light-logo" alt="logo">
      <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img light-logo1" style="margin-left: 50%;" alt="logo">
      </a><!-- LOGO -->
      <a aria-label="Hide Sidebar" class="app-sidebar__toggle ml-auto" data-toggle="sidebar" href="#"></a><!-- sidebar-toggle-->
    </div>

    @if(Auth::check())

    <ul class="side-menu">
         <div class="app-sidebar__user">
      <div class="dropdown user-pro-body text-center">
        <div class="user-pic pt-4">
          <img src="{{URL::to('/assets/uploads/admin.png')}}" alt="user-img" class="avatar-xl rounded-circle">
        </div>


        <div class="user-info">
          <h6 class=" mb-0 text-dark">{{Auth::guard('store')->user()->store_username}}</h6>
          <!---<span class="text-muted app-sidebar__user-name text-sm">{{Auth::user()->email}}</span>-->
        </div>
      </div>
    </div>
     <!-- <li><h3>Main</h3></li>-->
     
      <li><h3>Main</h3></li>
      <li class="slide">
        <a class="side-menu__item" href="{{url('store/home')}}"><i class="side-menu__icon ti-shield"></i><span class="side-menu__label"> Dashboard</span></a>
      </li>
      
       {{-- <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-panel"></i><span class="side-menu__label">{{ __('Masters') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">

        <li><a class="slide-item" href="{{route('store.list_attribute_group')}}">{{ __('Attribute Group') }}</a></li>
        <li><a class="slide-item" href="{{route('store.list_attribute_value')}}">{{ __('Attribute Value') }}</a></li>

       <li><a class="slide-item" href="{{route('store.list_agency')}}">{{ __('Agency') }}</a></li>
        </ul>
      </li> --}}


    <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_product')}}">
          <i class="side-menu__icon ti-shield"></i>
          <span class="side-menu__label"> {{ __('Products') }}</span>
        </a>
      </li>

       <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_inventory')}}">
          <i class="side-menu__icon ti-pencil-alt"></i>
          <span class="side-menu__label"> {{ __('Inventory Management') }}</span>
        </a>
      </li>

       <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_pos')}}">
          <i class="side-menu__icon ti-receipt"></i>
          <span class="side-menu__label"> {{ __('Point of Sale') }}</span>
        </a>
      </li>

      <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_order')}}">
          <i class="side-menu__icon ti-layers"></i>
          <span class="side-menu__label"> {{ __('Order') }}</span>
        </a>
      </li>

       <li class="slide">
        <a class="side-menu__item" href="{{route('store.store_admin')}}">
          <i class="side-menu__icon ti-layers"></i>
          <span class="side-menu__label"> {{ __('Store Admin') }}</span>
        </a>
      </li>




      <li><h3>General</h3></li>

       <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-settings"></i><span class="side-menu__label">{{ __('Settings') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">

         <li><a class="slide-item" href="{{route('store.settings')}}">{{ __('Settings') }}</a></li>
         <li><a class="slide-item" href="{{route('store.time_slots')}}">{{ __('Working Days') }}</a></li>
         <li><a class="slide-item" href="{{route('store.password')}}">{{ __('Change Password') }}</a></li>

        </ul>
      </li>

    </ul>

    @endif

  
  </aside>
  <!--/APP-SIDEBAR-->
