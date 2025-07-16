 @extends('layout.index')
 @section('content')
 

             <div class="row">
                 <div class="col-12">
                     <div class="page-title-box">
                         <div class="page-title-right">
                             <form class="d-flex">
                                 <div class="input-group">
                                     <input type="text" class="form-control form-control-light" id="dash-daterange">
                                     <span class="input-group-text bg-primary border-primary text-white">
                                         <i class="mdi mdi-calendar-range font-13"></i>
                                     </span>
                                 </div>
                                 <a href="javascript: void(0);" class="btn btn-primary ms-2">
                                     <i class="mdi mdi-autorenew"></i>
                                 </a>
                                 <a href="javascript: void(0);" class="btn btn-primary ms-1">
                                     <i class="mdi mdi-filter-variant"></i>
                                 </a>
                             </form>
                         </div>
                         <h4 class="page-title">Dashboard</h4>
                     </div>
                 </div>
             </div>

             <div class="row">
                 <div class="col-xl-5 col-lg-6">
                     @include('dashboard.components.widgets')
                 </div> <!-- end col -->

                 <div class="col-xl-7 col-lg-6">
                     @include('dashboard.components.revenueChart')
                     

                 </div> <!-- end col -->
             </div>
             <!-- end row -->

             <div class="row">
                 <div class="col-lg-8">
                     @include('dashboard.components.lineChart')
                     
                 </div> <!-- end col-->

                 <div class="col-lg-4">
                     @include('dashboard.components.donutChart')
                     
                 </div> <!-- end col-->
             </div>
             <!-- end row -->

             <!-- <div class="row">
                
                     
                 

                 

             </div> -->
             <!-- end row -->

         
 @endsection
 @section('script')
<script src="js/update-dashboard.js"></script>

 @endsection