<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('upload/favicon.png')}}">
    <title>Assets Management System</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.css')}}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/style.css')}}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{ asset('plugin/datatables2/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css')}}" type="text/css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />     -->
    <!-- Script -->
    <script src="{{ asset('js/jquery-3.5.1.min.js')}}"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

    <script src="{{ asset('js/popper.min.js')}}"></script>
    <script src="{{ asset('js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.js')}}"></script>
    <script src="{{ asset('js/jquery-ui.min.js')}}"></script>
    <script src="{{ asset('plugin/chart/moment.min.js')}}"></script>
    <script src="{{ asset('plugin/chart/Chart.min.js')}}"></script>
    <script src="{{ asset('plugin/chart/utils.js')}}"></script>
    <script src="{{ asset ('plugin/jqueryvalidation/jquery.validate.js')}}"></script>
    <script src="{{ asset('plugin/jqueryvalidation/additional-methods.js')}}"></script>
    <script src="{{ asset('plugin/datatables2/datatables.min.js')}}"></script>
    <script src="{{ asset('js/general.js')}}"></script>
    <script src="{{ asset('js/html5-qrcode.min.js')}}"></script>
    <script src="{{ asset('js/select2.min.js')}}"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script> -->




</head>

<body>

    <div class="sidebar">
        <div class="sidebar-wrapper">
            <div class="logo" style="text-align:center;">
                <img class="logoimg" src="" style="width:120px" />
                </a>
            </div>
            <ul class="nav">

                <li class="{{ Request::is( 'home') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'home') }}">
                        <p><img width="22"
                                src="<?php echo asset('images/icon-dashboard.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.dashboard'); ?>
                        </p>
                    </a>
                </li>


                <li class="{{ Request::is( 'assetlist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'assetlist') }}">
                        <p><img width="22"
                                src="<?php echo asset('images/icon-asset.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.assetmenu'); ?>
                        </p>
                    </a>
                </li>

                <li class="{{ Request::is( 'assetvehiclelist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'assetvehiclelist') }}">
                        <p><img width="22"
                                src="<?php echo asset('images/icon-asset.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.assetvehiclemenu'); ?>
                        </p>
                    </a>
                </li>

                <li class="{{ Request::is( 'componentlist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'componentlist') }}">
                        <p><img width="22"
                                src="<?php echo asset('images/icon-component.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.issue'); ?>
                        </p>
                    </a>
                </li>

                <li class="{{ Request::is( 'maintenancelist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'maintenancelist') }}">
                        <p><img width="22"
                                src="<?php echo asset('images/icon-maintenance.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.maintenancemenu'); ?>
                        </p>
                    </a>
                </li>

                <li class="{{ Request::is( 'inventorylist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'inventorylist') }}">
                        <p><img width="22"
                                src="<?php echo asset('images/icon-asset.png') ?>" />&nbsp;&nbsp;&nbsp;Inventory
                        </p>
                    </a>
                </li>

                <!-- No depreciation sa muntinlupa drrmo -->
                <!-- <li class="{{ Request::is( 'depreciationlist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'depreciationlist') }}">
                        <p><img width="22"
                                src="<?php echo asset('images/icon-depreciation.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.depreciationmenu'); ?>
                        </p>
                    </a>
                </li> -->
                <!-- 
                <li>
                    <a data-toggle="collapse" href="#utilities"
                        class="{{ Request::is( 'utilities/profile') || Request::is( 'utilities/allusers') || Request::is( 'utilities/application') ? '' : 'collapsed' }}"
                        aria-expanded="{{Request::is( 'utilities/profile') || Request::is( 'utilities/allusers') || Request::is( 'utilities/application') ? 'true' : 'false' }}">
                        <i class="ti-settings"></i>
                        <p><img width="25"
                                src="<?php echo asset('images/icon-setting.png') ?>" />&nbsp;&nbsp;&nbsp;Utilities
                        </p>
                    </a>
                    <div class="{{ Request::is( 'utilities/profile') || Request::is( 'utilities/allusers') || Request::is( 'utilities/application') ? 'collapse in' : 'collapse' }}"
                        id="utilities"
                        aria-expanded="{{ Request::is( 'utilities/profile') || Request::is( 'utilities/allusers') || Request::is( 'utilities/application') ? 'true' : 'false' }}"
                        style="{{ Request::is( 'utilities/profile') || Request::is( 'utilities/allusers') || Request::is( 'utilities/application') ? '' : 'height: 0px;' }}">
                        <ul class="nav">
                            
                            
                            <li class="{{ Request::is( 'userlist') ? 'active' : '' }}">
                                <a href="{{ URL::to( 'userlist') }}">
                                    <span class="sidebar-mini"><i class="fa fa-angle-right"></i></span>
                                    <span class="sidebar-normal"><?php echo trans('lang.usermenu'); ?></span>
                                </a>
                            </li>
                         
                            <li class="{{ Request::is( 'settinglist') ? 'active' : '' }}">
                                <a href="{{ URL::to( 'settinglist') }}">
                                    <span class="sidebar-mini"><i class="fa fa-angle-right"></i></span>
                                    <span class="sidebar-normal"><?php echo trans('lang.applicationmenu'); ?></span>
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </li> -->


                <!-- removed this and moved to utilities module -->

                <!-- <li class="{{ Request::is( 'assettypelist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'assettypelist') }}">
                        <p><img width="22"
                                src="<?php echo asset('images/icon-type.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.assettypemenu'); ?>
                        </p>
                    </a>
                </li>
             
                <li class="{{ Request::is( 'brandlist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'brandlist') }}">
                        <p><img width="25"
                                src="<?php echo asset('images/icon-manufacturer.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.brandmenu'); ?>
                        </p>
                    </a>
                </li>
                
                <li class="{{ Request::is( 'supplierlist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'supplierlist') }}">
                        <p><img width="25"
                                src="<?php echo asset('images/icon-supplier.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.suppliermenu'); ?>
                        </p>
                    </a>
                </li>

                <li class="{{ Request::is( 'locationlist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'locationlist') }}">
                        <p><img width="25"
                                src="<?php echo asset('images/icon-location.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.locationmenu'); ?>
                        </p>
                    </a>
                </li>
                <li class="{{ Request::is( 'employeeslist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'employeeslist') }}">
                        <p><img width="25"
                                src="<?php echo asset('images/icon-employee.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.employeemenu'); ?>
                        </p>
                    </a>
                </li>

                <li class="{{ Request::is( 'departmentlist') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'departmentlist') }}">
                        <p><img width="20"
                                src="<?php echo asset('images/icon-department.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.departmentmenu'); ?>
                        </p>
                    </a>
                </li> -->


                <li class="{{ Request::is( 'reports/allreports') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'reports/allreports') }}">
                        <p><img width="25"
                                src="<?php echo asset('images/icon-report.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.reportmenu'); ?>
                        </p>
                    </a>
                </li>

                <li class="{{ Request::is( 'utilities/allutilities') ? 'active' : '' }}">
                    <a href="{{ URL::to( 'utilities/allutilities') }}">
                        <p><img width="25"
                                src="<?php echo asset('images/utilities.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.utilitiesmenu'); ?>
                        </p>
                    </a>
                </li>


                <li>
                    <a data-toggle="collapse" href="#settings"
                        class="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? '' : 'collapsed' }}"
                        aria-expanded="{{Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? 'true' : 'false' }}">
                        <i class="ti-settings"></i>
                        <p><img width="25"
                                src="<?php echo asset('images/icon-setting.png') ?>" />&nbsp;&nbsp;&nbsp;<?php echo trans('lang.settingmenu'); ?>
                        </p>
                    </a>
                    <div class="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? 'collapse in' : 'collapse' }}"
                        id="settings"
                        aria-expanded="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? 'true' : 'false' }}"
                        style="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? '' : 'height: 0px;' }}">
                        <ul class="nav">


                            <li class="{{ Request::is( 'userlist') ? 'active' : '' }}">
                                <a href="{{ URL::to( 'userlist') }}">
                                    <span class="sidebar-mini"><i class="fa fa-angle-right"></i></span>
                                    <span class="sidebar-normal"><?php echo trans('lang.usermenu'); ?></span>
                                </a>
                            </li>

                            <li class="{{ Request::is( 'settinglist') ? 'active' : '' }}">
                                <a href="{{ URL::to( 'settinglist') }}">
                                    <span class="sidebar-mini"><i class="fa fa-angle-right"></i></span>
                                    <span class="sidebar-normal"><?php echo trans('lang.applicationmenu'); ?></span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>


    <div class="main-panel">
        <nav class="navbar navbar-expand-lg navbar-light bg-light pl-4 pr-4">


            <div class="col-md-6 ">
                <a class="navbar-brand company" href="#"></a>
                <button class="navbar-toggler nav-toggler-mobile" type="button" data-toggle="collapse" data-target="#menu" aria-controls="menu"
                    aria-expanded="false" aria-label="">
                    <span class="navbar-toggler-icon"></span>
                </button>

            </div>
            <div class="col-md-6 ">
                <!--responsive-->
                <div class="collapse" id="menu">
                    <ul class="nav navmobile">

                        <li class="{{ Request::is( 'home') ? 'active' : '' }}">
                            <a href="{{ URL::to( 'home') }}">
                                <p><?php echo trans('lang.dashboard'); ?>
                                </p>
                            </a>
                        </li>


                        <li class="{{ Request::is( 'assetlist') ? 'active' : '' }}">
                            <a href="{{ URL::to( 'assetlist') }}">
                                <p><?php echo trans('lang.assetmenu'); ?>
                                </p>
                            </a>
                        </li>
                        <li class="{{ Request::is( 'assetvehiclelist') ? 'active' : '' }}">
                            <a href="{{ URL::to( 'assetvehiclelist') }}">
                                <p><?php echo trans('lang.assetvehiclemenu'); ?>
                                </p>
                            </a>
                        </li>

                        <li class="{{ Request::is( 'componentlist') ? 'active' : '' }}">
                            <a href="{{ URL::to( 'componentlist') }}">
                                <p><?php echo trans('lang.issue'); ?>
                                </p>
                            </a>
                        </li>

                        <li class="{{ Request::is( 'maintenancelist') ? 'active' : '' }}">
                            <a href="{{ URL::to( 'maintenancelist') }}">
                                <p><?php echo trans('lang.maintenancemenu'); ?>
                                </p>
                            </a>
                        </li>

                        <li class="{{ Request::is( 'inventorylist') ? 'active' : '' }}">
                            <a href="{{ URL::to( 'inventorylist') }}">
                                <p>Inventory
                                </p>
                            </a>
                        </li>

                        <!-- No depreciation sa muntinlupa drrmo -->

                        <!-- <li class="{{ Request::is( 'depreciationlist') ? 'active' : '' }}">
                                    <a href="{{ URL::to( 'depreciationlist') }}">
                                        <p><?php echo trans('lang.depreciationmenu'); ?>
                                        </p>
                                    </a>
                                </li> -->


                        <!-- remove this and moved to utilities module -->
                        <!-- <li class="{{ Request::is( 'assettypelist') ? 'active' : '' }}">
                                    <a href="{{ URL::to( 'assettypelist') }}">
                                        <p><?php echo trans('lang.assettypemenu'); ?>
                                        </p>
                                    </a>
                                </li>
                             
                                <li class="{{ Request::is( 'brandlist') ? 'active' : '' }}">
                                    <a href="{{ URL::to( 'brandlist') }}">
                                        <p><?php echo trans('lang.brandmenu'); ?>
                                        </p>
                                    </a>
                                </li>
                                
                                <li class="{{ Request::is( 'supplierlist') ? 'active' : '' }}">
                                    <a href="{{ URL::to( 'supplierlist') }}">
                                        <p><?php echo trans('lang.suppliermenu'); ?>
                                        </p>
                                    </a>
                                </li>

                                <li class="{{ Request::is( 'locationlist') ? 'active' : '' }}">
                                    <a href="{{ URL::to( 'locationlist') }}">
                                        <p><?php echo trans('lang.locationmenu'); ?>
                                        </p>
                                    </a>
                                </li>
                                <li class="{{ Request::is( 'employeeslist') ? 'active' : '' }}">
                                    <a href="{{ URL::to( 'employeeslist') }}">
                                        <p><?php echo trans('lang.employeemenu'); ?>
                                        </p>
                                    </a>
                                </li>

                                <li class="{{ Request::is( 'departmentlist') ? 'active' : '' }}">
                                    <a href="{{ URL::to( 'departmentlist') }}">
                                        <p><?php echo trans('lang.departmentmenu'); ?>
                                        </p>
                                    </a>
                                </li> -->


                        <li class="{{ Request::is( 'reports/allreports') ? 'active' : '' }}">
                            <a href="{{ URL::to( 'reports/allreports') }}">
                                <p><?php echo trans('lang.reportmenu'); ?>
                                </p>
                            </a>
                        </li>

                        <li class="{{ Request::is( 'utilities/allrutilities') ? 'active' : '' }}">
                            <a href="{{ URL::to( 'utilities/allrutilities') }}">
                                <p><?php echo trans('lang.utilitiesmenu'); ?>
                                </p>
                            </a>
                        </li>

                        @php
                        $user = Auth::user();
                        @endphp

                        @if($user && $user->role == '1')
                        <li>
                            <a data-toggle="collapse" href="#settingsmob"
                                class="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? '' : 'collapsed' }}"
                                aria-expanded="{{Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? 'true' : 'false' }}">
                                <i class="ti-settings"></i>
                                <p><?php echo trans('lang.settingmenu'); ?></p>
                            </a>
                            <div class="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? 'collapse in' : 'collapse' }}"
                                id="settingsmob"
                                aria-expanded="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? 'true' : 'false' }}"
                                style="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? '' : 'height: 0px;' }}">
                                <ul class="nav">
                                    <li class="{{ Request::is( 'userlist') ? 'active' : '' }}">
                                        <a href="{{ URL::to( 'userlist') }}">
                                            <span class="sidebar-mini"><i class="fa fa-angle-right"></i></span>
                                            <span class="sidebar-normal"><?php echo trans('lang.usermenu'); ?></span>
                                        </a>
                                    </li>

                                    <li class="{{ Request::is( 'settinglist') ? 'active' : '' }}">
                                        <a href="{{ URL::to( 'settinglist') }}">
                                            <span class="sidebar-mini"><i class="fa fa-angle-right"></i></span>
                                            <span class="sidebar-normal"><?php echo trans('lang.applicationmenu'); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endif


                        <!-- <li>
                                    <a data-toggle="collapse" href="#settingsmob"
                                        class="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? '' : 'collapsed' }}"
                                        aria-expanded="{{Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? 'true' : 'false' }}">
                                        <i class="ti-settings"></i>
                                        <p><?php echo trans('lang.settingmenu'); ?>
                                        </p>
                                    </a>
                                    <div class="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? 'collapse in' : 'collapse' }}"
                                        id="settingsmob"
                                        aria-expanded="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? 'true' : 'false' }}"
                                        style="{{ Request::is( 'settings/profile') || Request::is( 'settings/allusers') || Request::is( 'settings/application') ? '' : 'height: 0px;' }}">
                                        <ul class="nav">
                                            
                                            
                                            <li class="{{ Request::is( 'userlist') ? 'active' : '' }}">
                                                <a href="{{ URL::to( 'userlist') }}">
                                                    <span class="sidebar-mini"><i class="fa fa-angle-right"></i></span>
                                                    <span class="sidebar-normal"><?php echo trans('lang.usermenu'); ?></span>
                                                </a>
                                            </li>
                                         
                                            <li class="{{ Request::is( 'settinglist') ? 'active' : '' }}">
                                                <a href="{{ URL::to( 'settinglist') }}">
                                                    <span class="sidebar-mini"><i class="fa fa-angle-right"></i></span>
                                                    <span class="sidebar-normal"><?php echo trans('lang.applicationmenu'); ?></span>
                                                </a>
                                            </li>
                                            
                                        </ul>
                                    </div>
                                </li> -->

                    </ul>
                </div>
                <!--end responsive-->
                <ul class="topmenu float-md-right float-sm-left">
                    <li>

                        <span class="sidebar-mini"><i class="fa fa-user"></i></span>
                        <span class="sidebar-normal"><?php echo trans('lang.welcome'); ?>, {{ Auth::user()->fullname }} &nbsp;&nbsp;&nbsp;</span>

                    </li>
                    <li>
                        <a href="{{ URL::to( 'logout') }}">
                            <span class="sidebar-mini"><i class="fa fa-sign-out"></i></span>
                            <span class="sidebar-normal"><?php echo trans('lang.logout'); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        @yield('content')
        <footer class="footer">
            <div class="container-fluid">

                <div class="copyright pull-right">
                    © 2022 <span class="company"></span></a>
                </div>
            </div>
        </footer>
    </div>

    <script>
        (function($) {
            "use strict";

            //get height of the main container
            setTimeout(function() {
                $('.sidebar .sidebar-wrapper').css('height', $('.main-panel').outerHeight() + 'px');
            }, 2500);


            //get app setting
            $.ajax({
                type: "GET",
                url: "{{ url('settings')}}",
                dataType: "JSON",
                success: function(data) {
                    $("#id").val('1');
                    $(".company").html(data.data.company);
                    $(".setcurrency").html(data.data.currency);
                    $(".logoimg").attr("src", data.logo);
                }
            });
            //datepicker
            $('.setdate').datepicker({
                autoclose: true,
                dateFormat: "yy-mm-dd",
                todayHighlight: true
            });

        })(jQuery);
    </script>

</body>

</html>