<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <a href="index.html" class="logo logo-light">
        <span class="logo-lg">
            <img src="images/logo.png" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="images/logo-sm.png" alt="small logo">
        </span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="index.html" class="logo logo-dark">
        <span class="logo-lg">
            <img src="images/logo-dark.png" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="images/logo-dark-sm.png" alt="small logo">
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </div>

    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -->
    <div class="h-100" id="leftside-menu-container" data-simplebar="">
        <!-- Leftbar User -->
        <div class="leftbar-user">
            <a href="pages-profile.html">
                <img src="images/avatar-1.jpg" alt="user-image" height="42" class="rounded-circle shadow-sm">
                <span class="leftbar-user-name mt-2">Dominic Keller</span>
            </a>
        </div>

        <!--- Sidemenu -->
        <ul class="side-nav">

            <li class="side-nav-title">Navigation</li>

            <li class="side-nav-item">
                <a  href="{{route('dashboard')}}"  class="side-nav-link">
                    <i class="uil-home-alt"></i>
                    <span class="badge bg-success float-end">5</span>
                    <span> Dashboards </span>
                </a>
            </li>

            <li class="side-nav-title">Apps</li>

            <li class="side-nav-item">
                <a href="{{route('devices.index')}}" class="side-nav-link">
                    <i class="ri-smartphone-line"></i>
                    <span> Device </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{route('analytics.index')}}" class="side-nav-link">
                    <i class="ri-bar-chart-grouped-line"></i>
                    <span> Analystic </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{route('listhand.index')}}" class="side-nav-link">
                    <i class=" ri-file-list-3-line"></i>
                    <!-- <span class="badge bg-danger text-white float-end">New</span> -->
                    <span> Danh sách hand bài </span>
                </a>
                
            </li>

            


            <!-- Help Box -->
            <div class="help-box text-white text-center">
                <a href="javascript: void(0);" class="float-end close-btn text-white">
                    <i class="mdi mdi-close"></i>
                </a>
                <img src="images/help-icon.svg" height="90" alt="Helper Icon Image">
                <h5 class="mt-3">Unlimited Access</h5>
                <p class="mb-3">Upgrade to plan to get access to unlimited reports</p>
                <a href="javascript: void(0);" class="btn btn-secondary btn-sm">Upgrade</a>
            </div>
            <!-- end Help Box -->


        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>