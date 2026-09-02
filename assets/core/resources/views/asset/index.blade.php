@extends('main')
@section('content')

<style>
    .select2 {
        width: 100% !important;
        /* height: 100% !important; */
        border-radius: 5px;
    }

    .select2-container--default .select2-selection--single {
        /* border: var(--bs-border-width) solid var(--bs-border-color);
        background: transparent;
        padding: 0.3rem !important; */
        height: calc(2.5rem + 2px) !important;
    }

    /* ced4da */

    .select2-container--default .select2-results>.select2-results__options {
        background-color: var(--bs-body-bg);
    }

    .select2-container--default .select2-results__option--selected {
        background-color: transparent;
    }

    .select2-search--dropdown {
        background-color: var(--bs-body-bg);
        border: var(--bs-border-width) solid var(--bs-border-color);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: var(--bs-body-bg);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #aaa;
    }

    .select2-container--default.select2-container--open.select2-container--below .select2-selection--single {
        border: 1px solid #86b7fe;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--bs-body-color);
        line-height: 45px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 55px;
        right: 12px;
    }

    /* Select 2 Multiple  */
    .select2-container--default .select2-selection--multiple {
        /* height: 55px; */
        border: var(--bs-border-width) solid var(--bs-border-color);
        background-color: transparent;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        background-color: transparent;
        border: 1px solid #86b7fe;
        outline: 0;
    }

    .select2-container--open .select2-dropdown--below {
        border: 1px solid #86b7fe;
    }

    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #4079d6;
        color: white;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        border: var(--bs-border-width) solid var(--bs-border-color);
        background-color: transparent;
        border-radius: 5px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        border-right: none;
        padding-left: 5px;
        color: red;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: red;
        background-color: var(--bs-body-bg);
    }
</style>


<section class="">
    <div class="content p-4">
        <div class="row pt-3">
            <div class="col-md-6">
                <h3 class=""><?php echo trans('lang.asset_list'); ?></h3>
            </div>
            <div class="col-md-6 text-md-right pb-md-0 pb-3">
                <button type="button" data-toggle="modal" data-target="#scanning" class="btn btn-sm btn-fill btn-primary"><i class="fa fa-plus"></i> <?php echo trans('lang.scan_data'); ?></button>
                <button type="button" data-toggle="modal" data-target="#add" class="btn btn-sm btn-fill btn-primary"><i class="fa fa-plus"></i> <?php echo trans('lang.add_data'); ?></button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body ">
                        <div id="checkoutsuccess" class="display-none alert alert-success"><?php echo trans('lang.data_checkout_succeess'); ?></div>
                        <div id="checkinsuccess" class="display-none alert alert-success"><?php echo trans('lang.data_checkin_succeess'); ?></div>
                        <div id="messagesuccess" class="display-none alert alert-success"><?php echo trans('lang.data_added'); ?></div>
                        <div id="messagedelete" class="display-none alert alert-success"><?php echo trans('lang.data_deleted'); ?></div>
                        <div id="messageupdate" class="display-none alert alert-success"><?php echo trans('lang.data_updated'); ?></div>
                        <div id="messagescansuccess" class="display-none alert alert-success"><?php echo trans('lang.data_scan_succeess'); ?></div>
                        <div id="messagescaninvalid" class="display-none alert alert-danger"><?php echo trans('lang.data_scan_invalid'); ?></div>
                        <div class="table-responsive">
                            <table id="data" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Picture</th>
                                        <th>Name</th>
                                        <th>Total</th>

                                        <th>All Tags</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Picture</th>

                                        <th>Name</th>
                                        <th>Total</th>
                                        <th>All Tags</th>

                                        <th>Action</th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>

                        <!-- <div class="table-responsive">
                            <table id="data" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.picture'); ?></th>
                                        <th><?php echo trans('lang.assettag'); ?></th>
                                        <th><?php echo trans('lang.serial'); ?></th>
                                        <th><?php echo trans('lang.purchasedate'); ?></th>
                                        <th><?php echo trans('lang.cost'); ?></th>
                                        <th><?php echo trans('lang.description'); ?></th>
                                        <th><?php echo trans('lang.name'); ?></th>
                                        <th><?php echo trans('lang.type'); ?></th>
                                        <th>Category</th>
                                        <th><?php echo trans('lang.brand'); ?></th>
                                        <th>Location</th>
                                        <th><?php echo trans('lang.status'); ?></th>
                                        <th>status text</th>
                                        <th>Number of Days</th>
                                        <th><?php echo trans('lang.hstatus'); ?></th>
                                        <th><?php echo trans('lang.action'); ?></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.picture'); ?></th>
                                        <th><?php echo trans('lang.assettag'); ?></th>
                                        <th><?php echo trans('lang.serial'); ?></th>
                                        <th><?php echo trans('lang.purchasedate'); ?></th>
                                        <th><?php echo trans('lang.cost'); ?></th>
                                        <th><?php echo trans('lang.description'); ?></th>
                                        <th><?php echo trans('lang.name'); ?></th>
                                        <th><?php echo trans('lang.type'); ?></th>
                                        <th><?php echo trans('lang.brand'); ?></th>
                                        <th>Category</th>
                                        <th>Location</th>
                                        <th><?php echo trans('lang.status'); ?></th>
                                        <th>status text</th>
                                        <th>Number of Days</th>
                                        <th><?php echo trans('lang.hstatus'); ?></th>
                                        <th><?php echo trans('lang.action'); ?></th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div id="password" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content"> -->
    <!-- <form action="#" id="formpass" enctype="multipart/form-data" autocomplete="off"> -->
    <!-- <div class="modal-header">

                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="display-none messageexist alert alert-success"><?php echo trans('lang.tag_exist'); ?></div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label style="font-size:13px;font-weight:600;">Please Enter Administrative Password!</label>
                            <input name="adminpassword" type="password" id="adminpassword" class=" form-control" required placeholder="" />
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="passwordsubmit">submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                </div> -->
    <!-- </form> -->
    <!-- </div>
        </div>
    </div> -->

    <!--add new data -->
    <div id="add" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="#" id="formadd" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-header">

                        <h5 class="modal-title"><?php echo trans('lang.add_data'); ?></h5>
                        <button type="button" class="reloaddata ml-3 badge badge-data text-white background-green">Reload</button>

                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="display-none messageexist alert alert-success"><?php echo trans('lang.tag_exist'); ?></div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.name'); ?></label>
                                <input name="name" type="text" id="name" class=" form-control" required placeholder="<?php echo trans('lang.name'); ?>" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.assettag'); ?></label>
                                <input name="assettag" type="text" id="assettag" class=" form-control" required placeholder="<?php echo trans('lang.assettag'); ?>" />
                            </div>
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.supplier'); ?></label>
                                <select name="supplierid" id="supplierid" required class="select2 selectCreate">
                                    <option value=""></option>
                                </select>

                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.location'); ?></label>
                                <select name="locationid" id="locationid" required class="select2 selectCreate">
                                    <option value=""></option>
                                    <!-- <option value="locationid">Add New Data </option> -->

                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.brand'); ?></label>
                                <select name="brandid" id="brandid" required class="select2 selectCreate">
                                    <option value=""></option>
                                    <!-- <option value="brandid">Add New Data </option> -->

                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- <div class="form-group col-md-6">
                                <label></label>
                                <input name="serial" type="text" id="serial" class="select2 selectCreate " required placeholder="" />
                            </div> --}}
                            <div class="form-group col-md-4">
                                <label>Unit</label>
                                <select name="unit" id="unit" required class="select2 selectCreate">
                                    <option value=""></option>
                                    <!-- <option value="unit">Add New Data </option> -->

                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Category</label>
                                <select name="category" id="category" required class="select2 selectCreate">
                                    <option value=""></option>
                                    <!-- <option value="category">Add New Data </option> -->

                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.assettype'); ?></label>
                                <select name="" id="typeid" required class="select2 selectCreate">
                                    <option value=""></option>
                                    <!-- <option value="typeid">Add New Data </option> -->
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6 mb-0">
                                <label for="cost" class="control-label"><?php echo trans('lang.cost'); ?></label>
                                <div class="input-group mb-0">
                                    <span class="input-group-addon setcurrency border-1" id="currency"></span>
                                    <input class="form-control number" required="" placeholder="<?php echo trans('lang.cost'); ?>" id="cost" name="cost" type="text">
                                </div>
                                <label class="error" for="cost"></label>
                            </div>
                            <div class="form-group col-md-6 mb-0">
                                <label for="purchasedate" class="control-label"><?php echo trans('lang.purchasedate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.purchasedate'); ?>" id="purchasedate" name="purchasedate" type="text">
                                    <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="purchasedate"></label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6 mb-0">
                                <label for="warranty" class="control-label"><?php echo trans('lang.warranty'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control number" required="" placeholder="<?php echo trans('lang.warranty'); ?>" id="warranty" name="warranty" type="text">
                                    <span class="input-group-addon border-1" id="warrantyyear"><?php echo trans('lang.month'); ?></span>
                                </div>
                                <label class="error" for="warranty"></label>
                            </div>


                            <div class="form-group col-md-6 mb-0">
                                <label><?php echo trans('lang.status'); ?></label>
                                <select name="status" id="status" required class="form-control">
                                    <option value=""><?php echo trans('lang.status'); ?></option>
                                    <option value="1">Operational</option>
                                    <option value="2">Turned In</option>
                                    <option value="3">Non-Operational</option>
                                    <option value="4">Non-Serviceable</option>
                                    <option value="5">Unserviceable</option>
                                    <option value="7">Lost</option>
                                    <option value="7">Out for Repair</option>
                                    <option value="8">Out for Maintenance</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.description'); ?></label>
                            <textarea class="form-control" name="description" id="description" placeholder="<?php echo trans('lang.description'); ?>"></textarea>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.picture'); ?></label>
                            <input name="picture" type="file" id="picture" class=" form-control" placeholder="<?php echo trans('lang.picture'); ?>" />
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="save"><?php echo trans('lang.save'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end add data-->

    <!--edit new data -->
    <div id="edit" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div id="passwordcontent" class="passwordcontent modal-content">
                <div class="modal-header">

                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="display-none messageexist alert alert-success"><?php echo trans('lang.tag_exist'); ?></div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label style="font-size:13px;font-weight:600;">Please Enter Administrative Password!</label>
                            <input name="adminpassword" type="password" id="adminpassword" class=" form-control" required placeholder="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="passwordsubmit">submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                </div>
            </div>
            <div class="modal-content display-none" id="editcontent">
                <form action="#" id="formedit" enctype="multipart/form-data">
                    <div class="modal-header">

                        <h5 class="modal-title"><?php echo trans('lang.edit_data'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="messageexist alert alert-success display-none"><?php echo trans('lang.tag_exist'); ?></div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.name'); ?></label>
                                <input name="name" type="text" id="editname" class=" form-control" required placeholder="<?php echo trans('lang.name'); ?>" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.assettag'); ?></label>
                                <input name="assettag" type="text" id="editassettag" class=" form-control" required placeholder="<?php echo trans('lang.assettag'); ?>" />
                            </div>
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.supplier'); ?></label>
                                <select name="supplierid" id="editsupplierid" required class="form-control">
                                    <option value=""><?php echo trans('lang.supplier'); ?></option>
                                </select>
                            </div>

                        </div>
                        <div class="form-row">

                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.location'); ?></label>
                                <select name="locationid" id="editlocationid" required class="form-control">
                                    <option value=""><?php echo trans('lang.location'); ?></option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.brand'); ?></label>
                                <select name="brandid" id="editbrandid" required class="form-control">
                                    <option value=""><?php echo trans('lang.brand'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- <div class="form-group col-md-6">
                                <label></label>
                                <input name="serial" type="text" id="editserial" class="form-control " required placeholder=""" />
                            </div> --}}
                            <div class="form-group col-md-4">
                                <label>Unit</label>
                                <select name="editunit" id="editunit" required class="select2 selectCreate">
                                    <!-- <option value="">Units</option> -->
                                </select>


                            </div>
                            <div class="form-group col-md-4">
                                <label>Category</label>
                                <select name="editcategory" id="editcategory" required class="select2 selectCreate">
                                    <!-- <option value="">Category</option> -->
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.assettype'); ?></label>
                                <select name="typeid" id="edittypeid" required class="select2 selectCreate">
                                    <option value=""><?php echo trans('lang.assettype'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6 mb-0">
                                <label for="cost" class="control-label"><?php echo trans('lang.cost'); ?></label>
                                <div class="input-group mb-0">
                                    <span class="input-group-addon setcurrency border-1" id="editcurrency"></span>
                                    <input class="form-control number" required="" placeholder="<?php echo trans('lang.cost'); ?>" id="editcost" name="cost" type="text">
                                </div>
                                <label class="error" for="cost"></label>
                            </div>
                            <div class="form-group col-md-6 mb-0">
                                <label for="purchasedate" class="control-label"><?php echo trans('lang.purchasedate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.purchasedate'); ?>" id="editpurchasedate" name="purchasedate" type="text">
                                    <span class="input-group-addon border-1" id="editdate"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="purchasedate"></label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6 mb-0">
                                <label for="warranty" class="control-label"><?php echo trans('lang.warranty'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control number" required="" placeholder="<?php echo trans('lang.warranty'); ?>" id="editwarranty" name="warranty" type="text">
                                    <span class="input-group-addon border-1" id="editwarrantyyear"><?php echo trans('lang.month'); ?></span>
                                </div>
                                <label class="error" for="warranty"></label>
                            </div>

                            <div class="form-group col-md-6 mb-0">
                                <label><?php echo trans('lang.status'); ?></label>
                                <select name="status" id="editstatus" required class="form-control">
                                    <option value=""><?php echo trans('lang.status'); ?></option>
                                    <option value="1">Operational</option>
                                    <option value="2">Turned In</option>
                                    <option value="3">Non-Operational</option>
                                    <option value="4">Non-Serviceable</option>
                                    <option value="5">Unserviceable</option>
                                    <option value="6">Lost</option>
                                    <option value="7">Out for Repair</option>
                                    <option value="8">Out for Maintenance</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.description'); ?></label>
                            <textarea class="form-control" name="description" id="editdescription" placeholder="<?php echo trans('lang.description'); ?>"></textarea>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.picture'); ?></label>
                            <input name="picture" type="file" id="editpicture" class=" form-control" placeholder="<?php echo trans('lang.picture'); ?>" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" id="editid" />
                        <button type="submit" class="btn btn-primary" id="saveedit"><?php echo trans('lang.save'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end edit data-->



    <!--add checkout -->
    <div id="checkout" class="modal fade" role="dialog">
        <div class="modal-dialog ">
            <div class="modal-content">
                <form action="#" id="formcheckout" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-header">

                        <h5 class="modal-title"><?php echo trans('lang.checkout'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.assettag'); ?></label>
                                <input name="assettag" type="text" readonly id="checkoutassettag" class=" form-control" required placeholder="<?php echo trans('lang.assettag'); ?>" />
                            </div>
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.asset'); ?></label>
                                <input name="asset" type="text" readonly id="checkoutname" class=" form-control" required placeholder="<?php echo trans('lang.asset'); ?>" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.checkoutto'); ?></label>
                                <select name="employeeid" id="checkoutemployeeid" required class="form-control">
                                    <option value=""><?php echo trans('lang.employee'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label for="checkoutdate" class="control-label"><?php echo trans('lang.checkoutdate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.checkoutdate'); ?>" id="checkoutdate" name="checkoutdate" type="text">
                                    <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="checkoutdate"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.controlno'); ?></label>
                            <input class="form-control" name="controlno23" id="controlno23" placeholder="<?php echo trans('lang.controlno'); ?>"></input>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.receivedby'); ?></label>
                                <input class="form-control" id="receivedby" readonly name="receivedby" type="text">
                                <!-- <select name="receivedby" id="receivedby" required class="form-control">
                                    <option value="">Select</option>
                                </select> -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.remarks'); ?></label>
                            <textarea class="form-control" name="remarks1" id="remarks1" placeholder="<?php echo trans('lang.remarkshere'); ?>"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <!-- <input type="hidden" name="employeeid" id="checkinemployeeid" value="0" /> -->
                        <input type="hidden" name="assetid" id="assetid" />
                        <button type="submit" class="btn btn-primary" id="savecheckout"><?php echo trans('lang.save'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end checkout-->

    <!--add scanning -->
    <div id="scanning" class="modal fade" role="dialog">
        <div class="modal-dialog ">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title"><?php echo trans('lang.scan_data'); ?></h5>
                    <button type="button" class="reloaddata ml-3 badge badge-data text-white background-green">Reload</button>

                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Scan Item</label>
                            <input name="search" type="text" id="scansearch" class=" form-control" required placeholder="scan Item" />

                            <div id="scannedList" class="mt-3">
                                <label>Scanned Items:</label>
                                <ul class="list-group" id="scannedItems"></ul>
                            </div>

                        </div>
                    </div>
                    <form action="#" id="formscanning" enctype="multipart/form-data" autocomplete="off">
                        <div class="form-row" id="checkdata" style="display: none;">
                            <div class="form-group col-md-12">
                                <label>Asset Tag</label>
                                <input name="assettag" type="text" readonly id="checkinassettag" class=" form-control" required placeholder="<?php echo trans('lang.assettag'); ?>" />
                            </div>
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.asset'); ?></label>
                                <input name="asset" type="text" readonly id="checkinname" class=" form-control" required placeholder="<?php echo trans('lang.asset'); ?>" />
                            </div>

                            <!-- <div class="form-group col-md-12">
                                <label id="borrowername"></label>
                                <select name="checkoutemployeeid1" id="checkoutemployeeid1" required class="form-control">
                                    <option value=""></option>
                                </select>
                            </div> -->


                            <div class="form-group col-md-12">
                                <label id="borrowername"></label>
                                <select name="checkoutemployeeid1" id="checkoutemployeeid1" required class="select2 selectCreate">
                                    <option value=""></option>
                                </select>
                            </div>


                            <!-- <div class="form-group col-md-12">
                                <label><?php echo trans('lang.checkinto'); ?></label>
                                <select name="checkstatus" id="checkstatus" required class="form-control">
                                    <option value=""><?php echo trans('lang.employee'); ?></option>
                                </select>
                            </div> -->


                            <!-- <div class="form-group col-md-12 ">
                                <label for="checkindate" class="control-label"><?php echo trans('lang.checkindate'); ?></label>
                                <div class="input-group ">
                                    <input class="form-control setdate" readonly required="" placeholder="<?php echo trans('lang.checkindate'); ?>" id="checkindate" name="checkindate" type="text">
                                    <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="checkindate"></label>
                            </div> -->



                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.controlno'); ?></label>
                                <input class="form-control" name="controlno" id="controlno" placeholder="<?php echo trans('lang.controlno'); ?>"></input>
                            </div>
                            <!-- <div class="form-group col-md-6" id="returnerinput">
                                <label>Type of I.D</label>
                                <select name="typeofid" id="typeofid" required class="select2 selectCreate">
                                    <option value=""></option>
                                </select>
                            </div> -->

                            <!-- <div class="form-group col-md-6" id="returnerinput1">
                                <label>I.D Number</label>
                                <input class="form-control" name="idno" id="idno" placeholder="I.D Number"></input>
                            </div> -->
                            <div class="form-group col-md-6" id="returnerinput2">
                                <label>Department / Office Representing</label>
                                <select name="depid" id="depid" required class="select2 selectCreate">
                                    <option value=""></option>
                                </select>
                                <!-- <input class="form-control" name="depid" id="depid" placeholder="Department / Office Representing"></input> -->
                            </div>



                            <div class="form-group col-md-6">
                                <label id="personnelInCharge"></label>
                                <input class="form-control" name="receivedby1" id="receivedby1" readonly></input>
                                <!-- <select name="receivedby1" id="receivedby1" required class="form-control">
                                        <option value="">Select</option>
                                    </select> -->
                            </div>
                            <div id="datediv" class="form-group col-md-6">
                                <label id="dateid">Date:</label>
                                <div class="input-group mb-0">
                                    <input class="form-control" readonly required placeholder="Please select date"
                                        id="checkindate" name="checkindate" type="datetime-local">
                                    <span class="input-group-addon border-1" id="date"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <input class="form-control" style="display:none;" name="assetnumber" id="assetnumber" readonly></input>

                            <div class="form-group col-md-12" id="returnerinput3">
                                <label>Condition of Borrowed Equipment</label>
                                <!-- <input class="form-control" name="core" id="core" placeholder="Condition of Returned Equipment"></input> -->
                                <select name="core" id="core" required class="form-control">
                                    <option value=""></option>
                                    <option value="Serviceable">Serviceable</option>
                                    <option value="Under Maintenance">Under Maintenance</option>
                                    <option value="For Repair">For Repair</option>
                                    <option value="For Upgrade">For Upgrade</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12" id="returnerinput3">
                                <label>Purpose of Equipment</label>
                                <select name="used" id="used" required class="select2 selectCreate">
                                    <option value=""></option>
                                </select>
                                <!-- <select name="used" id="used" required class="form-control">
                                    <option value=""></option>
                                    <option value="Personal">Personal</option>
                                    <option value="Official">Official</option>
                                </select> -->
                            </div>

                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.remarks'); ?></label>
                                <textarea class="form-control" name="remarks" id="remarks" placeholder="<?php echo trans('lang.remarkshere'); ?>"></textarea>
                            </div>
                        </div>


                </div>
                <div class="modal-footer">
                    <!-- <input type="hidden" name="employeeid" id="checkinemployeeid1" value="0" /> -->
                    <input type="hidden" name="checkinassetid" id="checkinassetid" />
                    <!-- <button type="submit" style="display:none;" class="btn btn-primary" id="savescan"><?php echo trans('lang.save'); ?></button> -->
                    <button type="button" class="btn btn-success" id="saveAllScans">Save All</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div id="checkdata1" class="modal fade" role="dialog">
        <div class="modal-dialog ">
            <div class="modal-content">
                <form action="#" id="formcheck" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo trans('lang.scan_data'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Asset Tag</label>
                                <input name="assettag" type="text" readonly id="checkinassettag" class=" form-control" required placeholder="<?php echo trans('lang.assettag'); ?>" />
                            </div>
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.asset'); ?></label>
                                <input name="asset" type="text" readonly id="checkinname" class=" form-control" required placeholder="<?php echo trans('lang.asset'); ?>" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.checkinto'); ?></label>
                                <select name="employeeid1" id="checkoutemployeeid2" required class="form-control">
                                    <option value=""><?php echo trans('lang.employee'); ?></option>
                                </select>
                            </div>
                        </div>


                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label for="checkindate" class="control-label"><?php echo trans('lang.checkindate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.checkindate'); ?>" id="checkindate" name="checkindate" type="text">
                                    <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="checkindate"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.controlno'); ?></label>
                            <input class="form-control" name="controlno1" id="controlno1" placeholder="<?php echo trans('lang.controlno'); ?>"></input>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label></label>
                                <select name="receivedby1" id="receivedby1" required class="form-control">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.remarks'); ?></label>
                            <textarea class="form-control" name="remarks" id="remarks" placeholder="<?php echo trans('lang.remarkshere'); ?>"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- <input type="hidden" name="employeeid" id="checkinemployeeid1" value="0" /> -->
                        <input type="hidden" name="assetid" id="checkinassetid" />
                        <button type="submit" class="btn btn-primary" id="savecheckin"><?php echo trans('lang.save'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!--add checkin -->
    <div id="checkin" class="modal fade" role="dialog">
        <div class="modal-dialog ">
            <div class="modal-content">
                <form action="#" id="formcheckin" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo trans('lang.checkin'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.assettag'); ?></label>
                                <input name="assettag" type="text" readonly id="checkinassettag" class=" form-control" required placeholder="<?php echo trans('lang.assettag'); ?>" />
                            </div>
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.asset'); ?></label>
                                <input name="asset" type="text" readonly id="checkinname" class=" form-control" required placeholder="<?php echo trans('lang.asset'); ?>" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.checkinto'); ?></label>
                                <select name="employeeid1" id="checkoutemployeeid3" required class="form-control">
                                    <option value=""><?php echo trans('lang.employee'); ?></option>
                                </select>
                            </div>
                        </div>


                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label for="checkindate" class="control-label"><?php echo trans('lang.checkindate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.checkindate'); ?>" id="checkindate" name="checkindate" type="text">
                                    <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="checkindate"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.controlno'); ?></label>
                            <input class="form-control" name="controlno1" id="controlno1" placeholder="<?php echo trans('lang.controlno'); ?>"></input>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.receivedby'); ?></label>
                                <select name="receivedby1" id="receivedby1" required class="form-control">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.remarks'); ?></label>
                            <textarea class="form-control" name="remarks" id="remarks" placeholder="<?php echo trans('lang.remarkshere'); ?>"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- <input type="hidden" name="employeeid" id="checkinemployeeid1" value="0" /> -->
                        <input type="hidden" name="assetid" id="checkinassetid" />
                        <button type="submit" class="btn btn-primary" id="savecheckin"><?php echo trans('lang.save'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end checkin-->


    <!--delete data -->
    <div class="modal fade" id="delete" role="dialog">
        <div class="modal-dialog modal-sm">
            <div id="passwordcontent1" class="passwordcontent modal-content">
                <div class="modal-header">

                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="display-none messageexist alert alert-success"><?php echo trans('lang.tag_exist'); ?></div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label style="font-size:13px;font-weight:600;">Please Enter Administrative Password!</label>
                            <input name="adminpassword" type="password" id="adminpassword1" class=" form-control" required placeholder="" />
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="passwordsubmit1">submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                </div>
            </div>
            <div class="modal-content display-none" id="deletecontent">
                <form action="#" id="formdelete">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo trans('lang.delete'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p><?php echo trans('lang.delete_confirm'); ?></p>
                        <input type="hidden" value="" name="id" id="iddelete" />

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="delete"><?php echo trans('lang.delete'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end delete data -->
</section>

<script>
    (function($) {



        $('.select2').select2({
            dropdownParent: $('#add'),
            width: 'resolve'
        });

        var loggedInUserId = "{{ Auth::user()->fullname }}";


        $(document).ready(function() {
            var today = new Date();

            var day = String(today.getDate()).padStart(2, '0');
            var month = String(today.getMonth() + 1).padStart(2, '0');
            var year = today.getFullYear();

            var hours = String(today.getHours()).padStart(2, '0');
            var minutes = String(today.getMinutes()).padStart(2, '0');
            var seconds = String(today.getSeconds()).padStart(2, '0');

            var formattedDateTime = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes + ':' + seconds;

            function setCheckinDate(value) {
                $('input[name="checkindate"]').val(value);
            }

            // Set the value of all checkindate fields with the proper datetime-local format
            setCheckinDate(formattedDateTime);

            $('#scanning').on('show.bs.modal', function() {
                setCheckinDate(formattedDateTime);
            });

            $('#checkin').on('show.bs.modal', function() {
                setCheckinDate(formattedDateTime);
            });

            $('.select2').on('select2:select', function(e) {
                var selectedValue = e.params.data.id;

                if (selectedValue === 'typeid') {
                    var url = "{{ URL::to('assettypelist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'supplierid') {
                    var url = "{{ URL::to('supplierlist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'locationid') {
                    var url = "{{ URL::to('locationlist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'brandid') {
                    var url = "{{ URL::to('brandlist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'unit') {
                    var url = "{{ URL::to('unitlist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'editunit') {
                    var url = "{{ URL::to('unitlist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'editcategory') {
                    var url = "{{ URL::to('unitlist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'category') {
                    var url = "{{ URL::to('categorylist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'checkoutemployeeid1') {
                    var url = "{{ URL::to('employeeslist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'used') {
                    var url = "{{ URL::to('usedlist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'typeofid') {
                    var url = "{{ URL::to('typeofidlist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'depid') {
                    var url = "{{ URL::to('departmentlist') }}";
                    window.open(url, '_blank');
                }

            });


        });


        // assetstatus
        // 1 = operational
        // 2 = Turned In 
        // 2 = Non-Operational 
        // 4 = Non-Serviceable
        // 5 = Unserviceable
        // 6 = Lost
        // 7 = Out for Repair
        function assetstatus(status) {
            if (status == 1) {
                return "<span class='badge badge-data text-white background-green'>Operational</span>";
            } else if (status == 2) {
                return "<span class='badge badge-data text-white background-yellow'>Turned In</span>";

            } else if (status == 3) {
                return "<span class='badge badge-data text-white background-orange'>Non-Operational</span>";

            } else if (status == 4) {
                return "<span class='badge badge-data text-white background-orange'>Non-Serviceable</span>";

            } else if (status == 5) {
                return "<span class='badge badge-data text-white background-red'>Unserviceable</span>";

            } else if (status == 6) {
                return "<span class='badge badge-data text-white background-black'>Lost</span>";

            } else if (status == 7) {
                return "<span class='badge badge-data text-white background-blue'>Out for Repair</span>";

            } else if (status == 8) {
                return "<span class='badge badge-data text-white background-blue'>Under Maintenance</span>";

            } else {
                return "<span class='badge badge-data text-white background-gray'>Undefined</span>";
            }
        }

        function assetstatusText(status) {
            status = Number(status);
            switch (status) {
                case 1:
                    return "Operational";
                case 2:
                    return "Turned In";
                case 3:
                    return "Non-Operational";
                case 4:
                    return "Non-Serviceable";
                case 5:
                    return "Unserviceable";
                case 6:
                    return "Lost";
                case 7:
                    return "Out for Repair";
                case 8:
                    return "Maintenance";
                default:
                    return "Undefined";
            }
        }


        function parseAssetDate(dateValue) {
            if (!dateValue) return null;
            var normalized = String(dateValue).trim();
            if (normalized.length === 0) return null;

            // Handle "YYYY-MM-DD HH:mm:ss" values by converting to ISO-like format.
            if (normalized.indexOf(' ') > -1 && normalized.indexOf('T') === -1) {
                normalized = normalized.replace(' ', 'T');
            }

            // Force Philippine time (UTC+08:00) when timestamp has no timezone info.
            var hasTimezone = /Z$|[+\-]\d{2}:\d{2}$/.test(normalized);
            if (!hasTimezone) {
                normalized += "+08:00";
            }

            var parsed = new Date(normalized);
            if (isNaN(parsed.getTime())) return null;
            return parsed;
        }

        function daylapse(item) {
            if (Number(item.checkstatus) !== 2) {
                return "NA";
            }

            var borrowedAtRaw = item.checkoutdate || item.checkout_date || item.borrowed_at || item.updated_at || item
                .created_at;
            var borrowedAt = parseAssetDate(borrowedAtRaw);
            if (!borrowedAt) {
                return "NA";
            }

            var now = new Date();
            var diffTime = now.getTime() - borrowedAt.getTime();
            if (diffTime < 0) {
                return "0 hours ago";
            }

            var diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
            if (diffDays < 1) {
                var diffHours = Math.floor(diffTime / (1000 * 60 * 60));
                return diffHours + (diffHours === 1 ? " hour ago" : " hours ago");
            }

            return diffDays + (diffDays === 1 ? " day" : " days");
        }

        function historystatus(status, checkstatus) {
            if (status != 1) {
                return "<span class='badge badge-data text-white background-red'>Unavailable</span>";
            } else if (checkstatus == 2) {
                return "<span class='badge badge-data text-white background-red'>Borrowed</span>";
            } else {
                return "<span class='badge badge-data text-white background-blue'>Available</span>";
            }
        }
        "use strict";

        var table = $('#data').DataTable({
            dom: "<'row align-items-center mb-2'<'col-sm-6'B><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-4'l><'col-sm-4'i><'col-sm-4'p>>",
            buttons: [{
                    extend: 'copy',
                    text: 'Copy <i class="fa fa-files-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    exportOptions: {
                        columns: [1, 2, 3]
                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV <i class="fa fa-file-excel-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    exportOptions: {
                        columns: [1, 2, 3]
                    }
                },
                {
                    text: 'PDF <i class="fa fa-file-pdf-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    action: function() {
                        window.open("{{ url('/assetlist/print') }}", '_blank');
                    }
                },
                {
                    text: 'Print <i class="fa fa-print"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    action: function() {
                        window.open("{{ url('/assetlist/print') }}", '_blank');
                    }
                }
            ],
            ajax: {
                url: "{{ url('getGroupedAssets') }}",

            },
            columns: [{
                    data: 'pictures',
                    title: 'Picture',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    title: 'Asset Name'
                },
                {
                    data: 'total',
                    title: 'Total Items'
                },
                {
                    data: 'all_tags',
                    visible: false,
                    searchable: true
                },

                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
        function clearAssetTableSearch() {
            if (table.search()) {
                table.search('').draw();
            }
            $('#data_filter input')
                .val('')
                .attr('autocomplete', 'off')
                .attr('name', 'asset_table_search_' + Date.now());
        }
        clearAssetTableSearch();
        setTimeout(clearAssetTableSearch, 100);
        setTimeout(clearAssetTableSearch, 500);

        $('#data tbody').on('click', '.btn-show', function() {

            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var name = $(this).data('name');

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {

                $.get("{{ url('getAssetsByName') }}/" + encodeURIComponent(name), function(data) {

                    var html = '<table class="table table-bordered" style="width:100%">';
                    html += '<thead><tr>';
                    html += '<th>Asset Tag</th>';
                    html += '<th>Purchase Date</th>';
                    html += '<th>Description</th>';
                    html += '<th>Name</th>';
                    html += '<th>Type</th>';
                    html += '<th>Category</th>';
                    html += '<th>Brand</th>';
                    html += '<th>Location</th>';
                    html += '<th>Status</th>';
                    html += '<th>Number of Days</th>';
                    html += '<th>History Status</th>';
                    html += '<th>Action</th>';
                    html += '</tr></thead><tbody>';

                    data.forEach(function(item) {
                        html += '<tr>';
                        html += '<td>' + item.assettag + '</td>';
                        html += '<td>' + item.purchasedate + '</td>';
                        html += '<td>' + (item.description ?? '-') + '</td>';
                        html += '<td>' + item.name + '</td>';
                        html += '<td>' + item.type + '</td>';
                        html += '<td>' + item.categoryname + '</td>';
                        html += '<td>' + item.brand + '</td>';
                        html += '<td>' + item.location + '</td>';
                        html += '<td>' + assetstatusText(item.status) + '</td>';
                        html += '<td>' + daylapse(item) + '</td>';
                        html += '<td>' + historystatus(item.status, item.checkstatus) + '</td>';
                        html += '<td>' + item.action + '</td>';
                        html += '</tr>';
                    });

                    html += '</tbody></table>';

                    row.child(html).show();
                    tr.addClass('shown');

                });
            }
        });



        // old datatable code

        // "use strict";
        // $('#data').DataTable({
        //     ajax: "{{ url('asset')}}",
        //     columns: [{
        //             data: 'id',
        //             orderable: false,
        //             searchable: false,
        //             visible: false
        //         },
        //         {
        //             data: 'pictures'
        //         },
        //         {
        //             data: 'assettag'
        //         },
        //         {
        //             data: 'serial',
        //             orderable: false,
        //             searchable: false,
        //             visible: false
        //         },
        //         {
        //             data: 'purchasedate',
        //             orderable: false,
        //             searchable: false,
        //             visible: false
        //         },
        //         {
        //             data: 'cost',
        //             orderable: false,
        //             searchable: false,
        //             visible: false
        //         },

        //         {
        //             data: 'description',
        //             orderable: false,
        //             searchable: false,
        //             visible: false
        //         },
        //         {
        //             data: 'name'
        //         },
        //         {
        //             data: 'type'
        //         },
        //         {
        //             data: 'categoryname'
        //         },
        //         {
        //             data: 'brand'
        //         },
        //         {
        //             data: function(e) {
        //                 return e.checkstatus == 2 ? e.depid : '';
        //             }
        //         },
        //         {
        //             data: function(e) {
        //                 return assetstatus(e.status);
        //             },
        //             searchable: true
        //         },
        //         {
        //             data: function(e) {
        //                 return assetstatusText(e.status); // For search
        //             },
        //             visible: false,
        //             searchable: true
        //         },

        //         {
        //             data: function(e) {
        //                 return daylapse(e.checkstatus, e.updated_at);
        //             }
        //         },

        //         {
        //             data: function(e) {
        //                 return historystatus(e.checkstatus);
        //             },
        //         },

        //         {
        //             data: 'action',
        //             orderable: false,
        //             searchable: false
        //         }
        //     ],
        //     buttons: [{
        //             extend: 'copy',
        //             text: 'Copy <i class="fa fa-files-o"></i>',
        //             className: 'btn btn-sm btn-fill btn-info ',
        //             title: '<?php echo trans('lang.asset_list '); ?>',
        //             exportOptions: {
        //                 columns: [2, 3, 4, 5, 6, 7, 8, 9, 10]
        //             }
        //         },
        //         {
        //             extend: 'csv',
        //             text: 'CSV <i class="fa fa-file-excel-o"></i>',
        //             className: 'btn btn-sm btn-fill btn-info ',
        //             title: '<?php echo trans('lang.asset_list'); ?>',
        //             exportOptions: {
        //                 columns: [2, 3, 4, 5, 6, 7, 8, 9, 10]
        //             }
        //         },
        //         {
        //             extend: 'pdf',
        //             text: 'PDF <i class="fa fa-file-pdf-o"></i>',
        //             className: 'btn btn-sm btn-fill btn-info ',
        //             title: '<?php echo trans('lang.asset_list'); ?>',
        //             orientation: 'landscape',
        //             exportOptions: {
        //                 columns: [2, 3, 4, 5, 6, 7, 8, 9, 10]
        //             },
        //             customize: function(doc) {
        //                 doc.styles.tableHeader.alignment = 'left';
        //                 doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
        //                     .join('*').split('');
        //             }
        //         },
        //         {
        //             extend: 'print',
        //             title: '<?php echo trans('lang.asset_list'); ?>',
        //             className: 'btn btn-sm btn-fill btn-info ',
        //             text: 'Print <i class="fa fa-print"></i>',
        //             exportOptions: {
        //                 columns: [2, 3, 4, 5, 6, 7, 8, 9, 10]
        //             }
        //         }
        //     ],
        //     drawCallback: function() {
        //         $('.dataTables_filter input').unbind();
        //         $('.dataTables_filter input').bind('keyup', function(e) {
        //             var code = e.keyCode || e.which;
        //             table = $("#data").DataTable();
        //             if (code == 13) {
        //                 table.search(this.value).draw();

        //                 table.one('xhr', function() {
        //                     var response = table.ajax.json();
        //                     var recordsFiltered = response.recordsFiltered;
        //                     if (recordsFiltered > 0) {
        //                         $("#messagescansuccess").css('display', "block");
        //                         $("#messagescaninvalid").css('display', "none");

        //                         // console.log(table.search(this.value));
        //                         if (table.search(this.value) !== "") {
        //                             window.setTimeout(function() {
        //                                 $(".btnconfirm").click();
        //                                 window.setTimeout(function() {
        //                                     $(".btnbb").click();
        //                                 }, 1000);
        //                             }, 1000);
        //                         } else {
        //                             console.log("goods")
        //                         }

        //                         // Attach click event listener to filtered records
        //                         // $("#data tbody").on("click", ".btnconfirm", function() {
        //                         //     var d = table.row($(this).closest("tr")).data();
        //                         //     console.log(d);
        //                         // });

        //                     } else {

        //                         $("#messagescaninvalid").css('display', "block");
        //                         $("#messagescansuccess").css('display', "none");
        //                     }
        //                 });
        //             }
        //         });
        //     },

        // });


        function generateControl() {
            $.ajax({
                type: "GET",
                url: "{{ url('generateControlNumber')}}",
                data: {
                    prefix: "BF"
                },
                dataType: "json",
                success: function(response) {
                    $('#controlno').val(response.message);
                    $('#controlno23').val(response.message);
                },
                error: function(xhr) {
                    alert("Failed to generate control number.");
                }
            });
        }




        function showreference() {
            $("#supplierid, #editsupplierid, #locationid, #editlocationid, #brandid, #editbrandid, #typeid, #edittypeid, #unit, #category, #used, #depid, #typeofid,#checkoutemployeeid1").empty();

            //get all supplier
            $.ajax({
                type: "GET",
                url: "{{ url('listsupplier')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#supplierid").append($("<option></option>")
                        .attr("value", "")
                        .text(""));
                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.name);
                        $("#supplierid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                        $("#editsupplierid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                    });
                    $("#supplierid").append($("<option></option>")
                        .attr("value", "supplierid")
                        .text("Add New Data"));
                }
            });





            // $.ajax({
            //     type: "GET",
            //     url: "{{ url('listemployees')}}",
            //     dataType: "JSON",
            //     success: function(html) {
            //         $("#checkoutemployeeid1").append($("<option></option>")
            //             .attr("value", "")
            //             .text(""));
            //         var objs = html.message;
            //         jQuery.each(objs, function(index, record) {
            //             var id = decodeURIComponent(record.id);
            //             var name = decodeURIComponent(record.fullname);
            //             $("#checkoutemployeeid1").append($("<option></option>")
            //                 .attr("value", id)
            //                 .text(name));
            //         });
            //         $("#checkoutemployeeid1").append($("<option></option>")
            //             .attr("value", "checkoutemployeeid1")
            //             .text("Add New Data"));
            //     }
            // });

            // get all employee
            $.ajax({
                type: "GET",
                url: "{{ url('listemployees')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#checkoutemployeeid1").append($("<option></option>")
                        .attr("value", "")
                        .text(""));
                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.fullname);
                        // $("#checkinemployeeid").append($("<option></option>")
                        //     .attr("value", id)
                        //     .text(name));
                        // $("#checkinemployeeid1").append($("<option></option>")
                        //     .attr("value", id)
                        //     .text(name));
                        // $("#checkoutemployeeid").append($("<option></option>")
                        //     .attr("value", id)
                        //     .text(name));
                        $("#checkoutemployeeid1").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                    });
                    $("#checkoutemployeeid1").append($("<option></option>")
                        .attr("value", "checkoutemployeeid1")
                        .text("Add New Data"));

                    $('#checkoutemployeeid1').select2();
                    $('#checkoutemployeeid1').trigger('change');

                }
            });

            $.ajax({
                type: "GET",
                url: "{{ url('listused')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#used").append($("<option></option>")
                        .attr("value", "")
                        .text(""));
                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.used);
                        var name = decodeURIComponent(record.description);

                        $("#used").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                    });
                    $("#used").append($("<option></option>")
                        .attr("value", "used")
                        .text("Add New Data"));

                    $('#used').select2();
                    $('#used').trigger('change');

                }
            });


            //get all asset type
            $.ajax({
                type: "GET",
                url: "{{ url('listassettype')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#typeid").append($("<option></option>")
                        .attr("value", "")
                        .text(""));
                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.name);
                        $("#typeid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                        $("#edittypeid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                    });
                    $("#typeid").append($("<option></option>")
                        .attr("value", "typeid")
                        .text("Add New Data"));
                    $("#edittypeid").append($("<option></option>")
                        .attr("value", "typeid")
                        .text("Add New Data"));
                    $('#edittypeid').trigger('change');
                    $('#edittypeid').select2();
                }
            });

            //get all units
            $.ajax({
                type: "GET",
                url: "{{ url('listunit')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#unit").append($("<option></option>")
                        .attr("value", "")
                        .text(""));

                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.unit);
                        $("#unit").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                        $("#editunit").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                    });
                    $("#unit").append($("<option></option>")
                        .attr("value", "unit")
                        .text("Add New Data"));
                    $("#editunit").append($("<option></option>")
                        .attr("value", "unit")
                        .text("Add New Data"));

                    if (!$('#editunit').hasClass('select2-hidden-accessible')) {
                        $('#editunit').select2();
                    }

                    // $('#editunit').val(data.message.unit).trigger('change');
                    $('#editunit').trigger('change');
                    $('#editunit').select2();
                }
            });


            //get all category
            $.ajax({
                type: "GET",
                url: "{{ url('listcategory')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#category").append($("<option></option>")
                        .attr("value", "")
                        .text(""));

                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.category);
                        $("#category").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                        $("#editcategory").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                    });
                    $("#category").append($("<option></option>")
                        .attr("value", "category")
                        .text("Add New Data"));
                    $("#editcategory").append($("<option></option>")
                        .attr("value", "category")
                        .text("Add New Data"));

                    if (!$('#editcategory').hasClass('select2-hidden-accessible')) {
                        $('#editcategory').select2();
                    }

                    $('#editcategory').trigger('change');
                    $('#editcategory').select2();
                }
            });

            //get all brand 
            $.ajax({
                type: "GET",
                url: "{{ url('listbrand')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#brandid").append($("<option></option>")
                        .attr("value", "")
                        .text(""));
                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.name);
                        $("#brandid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                        $("#editbrandid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                    });
                    $("#brandid").append($("<option></option>")
                        .attr("value", "brandid")
                        .text("Add New Data"));
                }
            });

            //get all location 
            $.ajax({
                type: "GET",
                url: "{{ url('listlocation')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#locationid").append($("<option></option>")
                        .attr("value", "")
                        .text(""));
                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.name);
                        $("#locationid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                        $("#editlocationid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));


                    });
                    $("#locationid").append($("<option></option>")
                        .attr("value", "locationid")
                        .text("Add New Data"));
                }
            });

            $.ajax({
                type: "GET",
                url: "{{ url('listdepartment')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#depid").append($("<option></option>")
                        .attr("value", "")
                        .text(""));
                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.name);
                        $("#depid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                        $("#editdepid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));

                        $('#depid').trigger('change');
                        $('#depid').select2();

                    });
                    $("#depid").append($("<option></option>")
                        .attr("value", "depid")
                        .text("Add New Data"));
                }
            });

            // list of receiver
            $.ajax({
                type: "GET",
                url: "{{ url('listreceiver')}}",
                dataType: "JSON",
                success: function(html) {
                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.fullname);
                        $("#receivedby").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                        $("#receivedby1").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                        $("#editdepartment").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                    });

                }
            });
            $.ajax({
                type: "GET",
                url: "{{ url('listtypeofid')}}",
                dataType: "JSON",
                success: function(html) {
                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.name);
                        $("#typeofid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));

                        $('#typeofid').trigger('change');
                        $('#typeofid').select2();

                    });
                    $("#typeofid").append($("<option></option>")
                        .attr("value", "id")
                        .text("Add New Data"));

                }
            });
        }



        //generate product code
        // $.ajax({
        //     type: "GET",
        //     url: "{{ url('asset/generateproductcode')}}",
        //     dataType: "JSON",
        //     success: function(html) {
        //         var objs = html.message;
        //         $("#assettag").val(html.message);
        //     }
        // });

        //add data
        $("#formadd").validate({
            rules: {
                warranty: {
                    required: true,
                    digits: true,
                    maxlength: 2
                }
            },
            submitHandler: function(form) {

                var form = new FormData();
                var name = $("#name").val();
                var locationid = $("#locationid").val();
                var supplierid = $("#supplierid").val();
                var typeid = $("#typeid").val();
                var brandid = $("#brandid").val();
                var assettag = $("#assettag").val();
                var unit = $("#unit").val();
                var category = $("#category").val();
                var quantity = $("#quantity").val();
                var purchasedate = $("#purchasedate").val();
                var cost = $("#cost").val();
                var warranty = $("#warranty").val();
                var status = $("#status").val();
                var description = $("#description").val();
                var picture = $('#picture')[0].files[0];

                form.append('name', name);
                form.append('locationid', locationid);
                form.append('supplierid', supplierid);
                form.append('brandid', brandid);
                form.append('typeid', typeid);
                form.append('assettag', assettag);
                form.append('unit', unit);
                form.append('category', category);
                form.append('quantity', quantity);
                form.append('purchasedate', purchasedate);
                form.append('cost', cost);
                form.append('warranty', warranty);
                form.append('status', status);
                form.append('description', description);
                form.append('picture', picture);

                $.ajax({
                    type: "POST",
                    url: "{{ url('saveasset')}}",
                    data: form,
                    contentType: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.message == 'success') {
                            $("#messagesuccess").css({
                                'display': "block"
                            });
                            $('#add').modal('hide');
                            window.setTimeout(function() {
                                location.reload()
                            }, 2000);
                        }
                        if (data.message == 'exist') {
                            $(".messageexist").css({
                                'display': "block"
                            });
                        }
                    }
                });
            }
        });

        //edit data
        $("#formedit").validate({
            rules: {
                warranty: {
                    required: true,
                    digits: true,
                    maxlength: 2
                }
            },

            submitHandler: function(form) {
                var form = new FormData();
                var id = $("#editid").val();
                var name = $("#editname").val();
                var locationid = $("#editlocationid").val();
                var supplierid = $("#editsupplierid").val();
                var typeid = $("#edittypeid").val();
                var brandid = $("#editbrandid").val();
                var assettag = $("#editassettag").val();
                var unit = $("#editunit").val();
                var category = $("#editcategory").val();
                var quantity = $("#editquantity").val();
                var purchasedate = $("#editpurchasedate").val();
                var cost = $("#editcost").val();
                var warranty = $("#editwarranty").val();
                var status = $("#editstatus").val();
                var description = $("#editdescription").val();
                var picture = $('#editpicture')[0].files[0];


                form.append('id', id);
                form.append('name', name);
                form.append('locationid', locationid);
                form.append('supplierid', supplierid);
                form.append('brandid', brandid);
                form.append('typeid', typeid);
                form.append('assettag', assettag);
                form.append('unit', unit);
                form.append('category', category);
                form.append('quantity', quantity);
                form.append('purchasedate', purchasedate);
                form.append('cost', cost);
                form.append('warranty', warranty);
                form.append('status', status);
                form.append('description', description);
                form.append('picture', picture);

                $.ajax({
                    type: "POST",
                    url: "{{ url('updateasset')}}",
                    data: form,
                    contentType: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.message == 'success') {
                            $("#messageupdate").css({
                                'display': "block"
                            });
                            $('#edit').modal('hide');
                            window.setTimeout(function() {
                                location.reload()
                            }, 2000);
                        }
                        if (data.message == 'exist') {
                            $(".messageexist").css({
                                'display': "block"
                            });
                        }

                    }
                });
            }
        });



        //delete data
        // $("#formdelete").validate({
        //     submitHandler: function(form) {
        //         $.ajax({
        //             method: "POST",
        //             url: "{{ url('deleteasset')}}",
        //             data: $("#formdelete").serialize(),
        //             dataType: "JSON",
        //             success: function(data) {
        //                 console.log(data);
        //                 $("#messagedelete").css({
        //                     'display': "block"
        //                 });
        //                 $('#delete').modal('hide');
        //                 window.setTimeout(function() {
        //                     location.reload()
        //                 }, 2000)
        //             }
        //         });
        //     }
        // });




        //show edit data
        // $('#edit').on('show.bs.modal', function(e) {
        //     $("#password").modal("show");
        //     passvalidate();

        //     var $modal = $(this),
        //         id = $(e.relatedTarget).attr('customdata');
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ url('assetbyid')}}",
        //         data: {
        //             id: id
        //         },
        //         dataType: "JSON",
        //         success: function(data) {
        //             $("#editid").val(id);
        //             $("#editname").val(data.message.assetname);
        //             $("#editlocationid").val(data.message.locationid);
        //             $("#editsupplierid").val(data.message.supplierid);
        //             $("#editbrandid").val(data.message.brandid);
        //             $("#edittypeid").val(data.message.typeid);
        //             $("#editassettag").val(data.message.assettag);
        //             $("#editserial").val(data.message.serial);
        //             $("#editquantity").val(data.message.quantity);
        //             $("#editpurchasedate").val(data.message.purchasedate);
        //             $("#editcost").val(data.message.cost);
        //             $("#editwarranty").val(data.message.warranty);
        //             $("#editstatus").val(data.message.status);
        //             $("#editdescription").val(data.message.assetdescription);
        //         }
        //     });
        // });

        // administrator password
        // function passvalidate() {
        //     $("#password").validate({
        //         submitHandler: function(form) {
        //             inputPassword = $('#adminpassword').val();
        //             $.ajax({
        //                 method: "POST",
        //                 url: "{{ url('User/requestpass')}}",
        //                 data: {
        //                     password: inputPassword,
        //                     // _token: '{{ csrf_token() }}'
        //                 },
        //                 dataType: "JSON",
        //                 success: function(data) {
        //                     if (data.success == 'failed') {
        //                         alert("Not Goods");

        //                     } else {
        //                         alert("Goods");
        //                     }


        //                 }
        //             });
        //         }
        //     });

        // }



        // function addmodal() {
        //     const modalContent = '<div class="modal-header">' +
        //         '<h5 class="modal-title"></h5>' +
        //         '<button type="button" class="close" data-dismiss="modal">&times;</button></div>' +
        //         '<div class="modal-body"><div class="display-none messageexist alert alert-success"><?php echo trans('lang.tag_exist'); ?></div>' +
        //         '<div class="form-row"><div class="form-group col-md-12">' +
        //         '<label style="font-size:13px;font-weight:600;">Please Enter Administrative Password!</label>' +
        //         '<input name="adminpassword1" type="password" id="adminpassword1" class="form-control" required placeholder="" />' +
        //         '</div></div></div><div class="modal-footer">' +
        //         '<button type="submit" class="btn btn-primary passwordsubmit" id="passwordsubmit1">Submit</button>' +
        //         '<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button></div>;'
        //     $('#passwordcontent1').html(modalContent);
        //     if (eventHolder == "delete") {
        //         $('#passwordsubmit').attr('id', `passwordsubmit1`);

        //     } else if (eventHolder == "delete") {

        //     }
        // }




        var targetModalEvent = null;
        var eventHolder;
        var x;
        var currentEditId = null;
        var currentDeleteId = null;

        function setEditSelectValue(selector, value, text) {
            var $field = $(selector);
            if (value === undefined || value === null || value === '') {
                $field.val('').trigger('change');
                return;
            }

            if (!$field.find('option[value="' + value + '"]').length) {
                $field.append($('<option></option>')
                    .attr('value', value)
                    .text(text || value));
            }

            $field.val(value).trigger('change');
        }

        function showEditModal() {
            showreference();
            // $("#edit").prop('class', 'modal fade');
            if (currentEditId) {
                var $modal = $('#edit'),
                    id = currentEditId;
                $.ajax({
                    type: "POST",
                    url: "{{ url('assetbyid') }}",
                    data: {
                        id: id
                    },
                    dataType: "JSON",
                    success: function(data) {
                        if (!data || data.success === 'failed' || !data.message) {
                            alert('Unable to load asset details.');
                            return;
                        }
                        $("#editid").val(id);
                        $("#editname").val(data.message.assetname);
                        setEditSelectValue('#editlocationid', data.message.locationid, data.message.location);
                        setEditSelectValue('#editsupplierid', data.message.supplierid, data.message.supplier);
                        setEditSelectValue('#editbrandid', data.message.brandid, data.message.brand);
                        setEditSelectValue('#edittypeid', data.message.typeid, data.message.type);
                        $("#editassettag").val(data.message.assettag);
                        // $("#editunit").val(parseInt(data.message.unit));
                        setEditSelectValue('#editunit', data.message.unit);
                        setEditSelectValue('#editcategory', data.message.category, data.message.categoryname);
                        $("#editquantity").val(data.message.quantity);
                        $("#editpurchasedate").val(data.message.purchasedate);
                        $("#editcost").val(data.message.cost);
                        $("#editwarranty").val(data.message.warranty);
                        setEditSelectValue('#editstatus', data.message.status);
                        $("#editdescription").val(data.message.assetdescription || data.message.description);

                        // $("#editunit", "#editcategory", "#edittypeid").change();
                    }
                });
                if (!$('#edit').hasClass('show')) {
                    $('#edit').modal('show');
                }
                $("#editcontent").css('display', 'block');
                targetModalEvent = null;
                currentEditId = null;
            }
        }

        function showDeleteModal() {
            // $("#edit").prop('class', 'modal fade');
            if (currentDeleteId) {
                var $modal = $(this),
                    id = currentDeleteId;
                $("#iddelete").val(id);
                $("#formdelete").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            method: "POST",
                            url: "{{ url('deleteasset')}}",
                            data: $("#formdelete").serialize(),
                            dataType: "JSON",
                            success: function(data) {
                                $("#messagedelete").css({
                                    'display': "block"
                                });
                                $('#delete').modal('hide');
                                window.setTimeout(function() {
                                    location.reload()
                                }, 2000)
                            }
                        });
                    }
                });

                $('#delete').modal('show');
                $("#deletecontent").css('display', 'block');
                targetModalEvent = null;
                currentDeleteId = null;
            }
        }

        function submitPassword() {
            if (eventHolder == 'edit') {
                var inputPassword = $('#adminpassword').val();
            } else {
                var inputPassword = $('#adminpassword1').val();
            }
            $.ajax({
                method: "POST",
                url: "{{ url('User/requestpass') }}",
                data: {
                    password: inputPassword,
                    // _token: '{{ csrf_token() }}'
                },
                dataType: "JSON",
                success: function(data) {
                    if (data.success === 'failed') {
                        alert("Password is wrong");

                    } else {
                        alert("Password validated");
                        $(".passwordcontent").css('display', 'none');
                        if (eventHolder == 'edit') {
                            showEditModal();
                        } else if (eventHolder == 'delete') {
                            showDeleteModal();
                        } else {
                            alert("hahaha21321haha");

                        }

                    }
                    $("#adminpassword").val("");

                }
            });
        }

        $(document).on('click', '[data-target="#edit"][customdata]', function() {
            currentEditId = $(this).attr('customdata');
        });

        $(document).on('click', '[data-target="#delete"][customdata]', function() {
            currentDeleteId = $(this).attr('customdata');
        });

        // $("#edit #delete").on('hide.bs.modal', function() {
        //     $("#editcontent").css('display', 'none');
        // });


        // modals goes here
        // edit data
        $('#edit').on('show.bs.modal', function(e) {
            x = 1;
            // addmodal();
            eventHolder = 'edit';
            // e.preventDefault();
            // $("#edit").css('display', 'none');

            targetModalEvent = e;
            currentEditId = currentEditId || $(e.relatedTarget).attr('customdata');
            // $("#password").modal("show");

            $("#editcontent").css('display', 'none');
            $(".passwordcontent").css('display', 'block');
        });



        //show delete data
        $('#delete').on('show.bs.modal', function(e) {
            x = 2;
            // addmodal();
            eventHolder = 'delete';
            // e.preventDefault();
            // $("#edit").css('display', 'none');

            targetModalEvent = e;
            currentDeleteId = currentDeleteId || $(e.relatedTarget).attr('customdata');
            // $("#password").modal("show");
            $(".passwordcontent1").css('display', 'block');
        });


        $("#passwordsubmit").click(function() {
            submitPassword();
        })

        $("#passwordsubmit1").click(function() {
            submitPassword();
        })

        $(".reloaddata").click(function() {
            showreference();
        })



        //checkout
        // $("#formscanning").validate({
        //     submitHandler: function(form) {
        //         $.ajax({
        //             method: "POST",
        //             url: "{{ url('savescan')}}",
        //             data: $("#formscanning").serialize(),
        //             dataType: "JSON",
        //             success: function(data) {
        //                 console.log(data);
        //                 assetprintform(data);
        //                 $("#checkoutsuccess").css({
        //                     'display': "block"
        //                 });
        //                 $('#checkout').modal('hide');
        //                 // window.setTimeout(function() {
        //                 //     location.reload()
        //                 // }, 2000)
        //             }
        //         });
        //     }
        // });


        function fetchAssetPrintForm(id) {
            fetch("{{ url('assetprintform')}}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: id
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Unable to prepare printout.');
                    }
                    const disposition = response.headers.get('Content-Disposition');
                    let filename = 'asset_form.pdf';
                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        filename = disposition.split('filename=')[1].replace(/["']/g, '').trim();
                    }
                    return response.blob().then(blob => ({
                        blob,
                        filename
                    }));
                })
                .then(({
                    blob,
                    filename
                }) => {
                    const url = window.URL.createObjectURL(blob);
                    const downloadLink = document.createElement('a');
                    downloadLink.href = url;
                    downloadLink.download = filename;
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    downloadLink.remove();

                    const iframe = document.createElement('iframe');
                    iframe.style.position = 'fixed';
                    iframe.style.right = '0';
                    iframe.style.bottom = '0';
                    iframe.style.width = '0';
                    iframe.style.height = '0';
                    iframe.style.border = '0';
                    iframe.src = url;
                    iframe.onload = function() {
                        setTimeout(function() {
                            iframe.contentWindow.focus();
                            iframe.contentWindow.print();
                            setTimeout(function() {
                                window.URL.revokeObjectURL(url);
                                iframe.remove();
                            }, 3000);
                        }, 500);
                    };
                    document.body.appendChild(iframe);
                })
                .catch(error => {
                    console.error('Error preparing printout:', error);
                    alert('Unable to prepare printout.');
                });
        }


        $("#formscanning").validate({
            submitHandler: function(form) {
                $.ajax({
                    method: "POST",
                    url: "{{ url('savescan')}}",
                    data: $("#formscanning").serialize(),
                    dataType: "JSON",
                    success: function(data) {
                        fetchAssetPrintForm(data.id);
                        // console.log(data);

                        $("#checkoutsuccess").css({
                            'display': "block"
                        });
                        $('#checkout').modal('hide');
                        // Optional reload
                        window.setTimeout(function() {
                            location.reload()
                        }, 2000)
                    }
                });
            }
        });

        $('#saveAllScans').on('click', function() {
            if (scannedAssets.length === 0) {
                alert("No assets scanned.");
                return;
            }

            const $modal = $('#scanning');
            const employeeid = $modal.find('#checkoutemployeeid1').val();
            const depid = $modal.find('#depid').val();
            const checkindate = $modal.find('#checkindate').val();
            const condition = $modal.find('#core').val();
            const used = $modal.find('#used').val();
            const remarks = $modal.find('#remarks').val();
            const controlno = $modal.find('#controlno').val();
            const hasBorrowedItem = scannedAssets.some(asset => asset.checkstatus != 2);

            if (!employeeid) {
                alert("Please select borrower/returner name.");
                return;
            }
            if (!depid) {
                alert("Please select department / office representing.");
                return;
            }
            if (!checkindate) {
                alert("Please provide date.");
                return;
            }
            if (!condition) {
                alert("Please select condition of borrowed equipment.");
                return;
            }
            if (!used) {
                alert("Please select purpose of equipment.");
                return;
            }
            if (hasBorrowedItem && !controlno) {
                alert("Please provide control number.");
                return;
            }

            const enrichedAssets = scannedAssets.map(asset => ({
                ...asset,
                controlno: asset.checkstatus == 2 ? (asset.last_control_number || asset.controlno || '') : controlno,
                depid: depid,
                condition: condition,
                used: used,
                receivedby: loggedInUserId,
                checkindate: checkindate,
                remarks: remarks,
                employeeid: employeeid
            }));

            $.ajax({
                url: "{{ url('savescanbatch') }}",
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    assets: enrichedAssets
                }),
                success: function(response) {
                    $('#scanning').modal('hide');
                    // Wait for modal to fully close and show next tab, then open PDF
                    window.setTimeout(function() {
                        window.open("{{ url('assetprintform') }}/" + response.id, '_blank');
                    }, 500);
                    window.setTimeout(function() {
                        location.reload()
                    }, 2000)

                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    let message = 'Error occurred while saving scanned items.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        message = xhr.responseText.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
                    }
                    alert(message);
                }
            });


        });


        function openPreparedAssetPrintout(printWindow, printUrl) {
            if (!printUrl) {
                if (printWindow) {
                    printWindow.close();
                }
                return;
            }

            if (printWindow && !printWindow.closed) {
                printWindow.location.href = printUrl;
                return;
            }

            var fallbackWindow = window.open(printUrl, '_blank');
            if (!fallbackWindow) {
                alert('Borrowed/return form was saved. Please allow pop-ups, then open the printout again.');
            }
        }

        //checkout
        $("#formcheckout").validate({
            submitHandler: function(form) {
                $.ajax({
                    method: "POST",
                    url: "{{ url('savecheckout')}}",
                    data: $("#formcheckout").serialize(),
                    dataType: "JSON",
                    success: function(data) {
                        $("#checkoutsuccess").css({
                            'display': "block"
                        });
                        $('#checkout').modal('hide');
                        // Wait for modal to fully close, then open PDF
                        window.setTimeout(function() {
                            window.open(data.print_url, '_blank');
                        }, 500);
                        window.setTimeout(function() {
                            location.reload()
                        }, 2000)
                    },
                    error: function() {
                        alert('Failed to save checkout. Please try again.');
                    }
                });
            }
        });


        //checkin
        $("#formcheckin").validate({
            submitHandler: function(form) {
                $.ajax({
                    method: "POST",
                    url: "{{ url('savecheckin')}}",
                    data: $("#formcheckin").serialize(),
                    dataType: "JSON",
                    success: function(data) {
                        $("#checkinsuccess").css({
                            'display': "block"
                        });
                        $('#checkin').modal('hide');
                        // Wait for modal to fully close, then open PDF
                        window.setTimeout(function() {
                            window.open(data.print_url, '_blank');
                        }, 500);
                        window.setTimeout(function() {
                            location.reload()
                        }, 2000)
                    },
                    error: function() {
                        alert('Failed to save checkin. Please try again.');
                    }
                });
            }
        });

        //show checkout
        $('#checkout').on('show.bs.modal', function(e) {
            var $modal = $(this),
                id = $(e.relatedTarget).attr('customdata');
            $.ajax({
                type: "POST",
                url: "{{ url('assetbyid')}}",
                data: {
                    id: id
                },
                dataType: "JSON",
                success: function(data) {
                    $("#assetid").val(id);
                    $("#checkoutname").val(data.message.name);
                    $("#checkoutassettag").val(data.message.assettag);
                    generateControl();
                }
            });
        });

        //show checkin
        $('#checkin').on('show.bs.modal', function(e) {
            var $modal = $(this),
                id = $(e.relatedTarget).attr('customdata');
            $.ajax({
                url: "{{ url('savescanbatch') }}",
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    assets: enrichedAssets
                }),
                success: function(response) {
                    $('#scanning').modal('hide');
                    // Wait for modal to fully close and show next tab, then open PDF
                    window.setTimeout(function() {
                        window.open("{{ url('assetprintform') }}/" + response.id, '_blank');
                    }, 500);
                    window.setTimeout(function() {
                        location.reload()
                    }, 2000)

                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    let message = 'Error occurred while saving scanned items.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        message = xhr.responseText.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
                    }
                    alert(message);
                }
            });

        $('#scanning').on('hidden.bs.modal', function() {
            $("#checkdata").hide();
            $('#scannedItems').empty();
            $("#scansearch").val("");
            scannedAssets = [];
            $(this).removeData('bs.modal');
            $(this).find('form').trigger('reset');
            // $(this).find('.modal-body').html('');
        });

        $('#edit').on('hidden.bs.modal', function() {
            $("#editcontent").css('display', 'none');
            $("#supplierid, #editsupplierid, #locationid, #editlocationid, #brandid, #editbrandid, #typeid, #edittypeid, #editcategory, #editunit,#unit, #category, #used, #typeofid,#checkoutemployeeid1").empty();

        });


        //show checkin

        let scannedAssets = [];
        let scanSearchTimer = null;

        function runAssetScanLookup(showEmptyAlert = false) {
            generateControl();
            const searchValue = $('#scansearch').val().trim();

            if (searchValue.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('assetbytag') }}",
                    data: {
                        receivedby: loggedInUserId,
                        searchValue: searchValue
                    },
                    dataType: "JSON",
                    success: function(data) {
                        if (data.success === 'success' && data.message) {
                            const assetId = data.message.assetid;

                            // Check if asset already scanned
                            const alreadyScanned = scannedAssets.some(item => item.assetid === assetId);
                            if (!alreadyScanned) {
                                // Clone the asset data and add additional form fields
                                const isReturn = data.message.checkstatus == 2;
                                let assetData = {
                                    ...data.message, // include all properties from the scanned asset
                                    controlno: isReturn ? data.message.last_control_number : $('#controlno').val(),
                                    condition: $('#core').val(),
                                    used: $('#used').val(),
                                    receivedby: loggedInUserId,
                                    checkindate: $('#checkindate').val(),
                                    remarks: $('#remarks').val(),
                                    employeeid: $('#checkoutemployeeid1').val()
                                };

                                scannedAssets.push(assetData);
                                // Add to visual list
                                $('#scannedItems').append(`
                                    <li class="list-group-item">
                                        ${data.message.assetname} (${data.message.assettag})
                                    </li>
                                `);
                            }

                            // $("#savescan").show();
                            $("#checkdata").show();

                            $("#assetnumber").val(assetId);
                            $("#checkinname").val(data.message.assetname);
                            $("#checkinassettag").val(data.message.assettag);

                            console.log(data.message.checkstatus);

                            if (data.message.checkstatus == 0) {
                                $("#borrowername").html("Borrowers Name");
                                $("#personnelInCharge").html("Logistic Custodian:");
                            } else {
                                $("#borrowername").html("Returners Name");
                                $("#dateid").html("Returned Date");
                                $("#personnelInCharge").html("Logistic Custodian:");
                            }
                            $("#receivedby1").val(loggedInUserId);

                            alert("Asset: " + data.message.assetname + "\nAsset Tag: " + data.message.assettag);
                            $("#scansearch").val("");
                        }

                        // old scanning method
                        // if (data.success === 'success' && data.message) {
                        //     $("#savescan").show();
                        //     $("#checkdata").show();

                        //     $("#assetnumber").val(data.message.assetid);
                        //     $("#checkinname").val(data.message.assetname);
                        //     $("#checkinassettag").val(data.message.assettag);
                        //     // if (data.message.efullname != "null") {
                        //     //     $("#checkoutemployeeid1").val(data.message.ahemployeeid);
                        //     // } else {
                        //     //     $("#checkoutemployeeid1").val();
                        //     // }
                        //     if (data.message.checkstatus == 0) {
                        //         $("#borrowername").html("Borrowers Name");
                        //         // $("#dateid").html("Borrowed Date");
                        //         // $("#returnerinput").hide();
                        //         // $("#returnerinput1").hide();
                        //         // $("#returnerinput2").hide();
                        //         // $("#returnerinput3").hide();
                        //         // $("#datediv").addClass("col-md-12");

                        //     } else {
                        //         $("#borrowername").html("Returners Name");
                        //         $("#dateid").html("Returned Date");
                        //         // $("#returnerinput").show();
                        //         // $("#returnerinput1").show();
                        //         // $("#returnerinput2").show();
                        //         // $("#returnerinput3").show();
                        //         // $("#datediv").removeClass();
                        //         // $("#datediv").addClass("col-md-6");

                        //     }
                        //     $("#receivedby1").val(loggedInUserId);

                        //     alert("Asset: " + data.message.assetname + "\nAsset Tag: " + data.message.assettag);
                        // }
                        else {
                            $("#checkdata").hide();
                            if (data.assetstatus == "2") {
                                alert("Asset is Turned In");
                            } else if (data.assetstatus == "3") {
                                alert("Asset is Non-Operational");
                            } else if (data.assetstatus == "4") {
                                alert("Asset is Non-Serviceable");
                            } else if (data.assetstatus == "5") {
                                alert("Asset is Unserviceable");
                            } else if (data.assetstatus == "6") {
                                alert("Asset is Lost");
                            } else if (data.assetstatus == "7") {
                                alert("Asset is Out for Repair");
                            } else if (data.assetstatus == "8") {
                                alert("Asset is Out for Maintenance");
                            }
                            alert("No asset found with that ID or Tag.");
                        }
                        // assetstatus
                        // 1 = operational
                        // 2 = Turned In 
                        // 3 = Non-Operational
                        // 4 = Non-Serviceable
                        // 5 = Unserviceable
                        // 6 = Lost
                        // 7 = Out for Repair
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                        alert("Error retrieving asset details. Please try again.");
                    }
                });
            } else if (showEmptyAlert) {
                alert("Please enter a valid asset ID or Tag.");
            }
        }

        $('#scansearch').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                if (scanSearchTimer) {
                    clearTimeout(scanSearchTimer);
                }
                runAssetScanLookup(true);
            }
        });

        $('#scansearch').on('input', function() {
            if (scanSearchTimer) {
                clearTimeout(scanSearchTimer);
            }

            // Auto-search after scanner/user input settles, even without Enter key.
            scanSearchTimer = setTimeout(function() {
                runAssetScanLookup(false);
            }, 250);
        });





        // status change




        // // for scanner auto selection of item





    })(jQuery);
</script>
@endsection
