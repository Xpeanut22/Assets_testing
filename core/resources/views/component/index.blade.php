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
                <h3 class=""><?php echo trans('lang.issuelist'); ?></h3>
            </div>
            <div class="col-md-6 text-md-right pb-md-0 pb-3">
                <button type="button" data-toggle="modal" data-target="#batchscanning" class="btn btn-sm btn-fill btn-primary"><i class="fa fa-plus"></i>Scan Issue</button>
                <!-- <button type="button" data-toggle="modal" data-target="#batchcheckout" class="btn btn-sm btn-fill btn-primary"><i class="fa fa-plus"></i>Batch Issuance</button> -->
                <button type="button" data-toggle="modal" data-target="#add" class="btn btn-sm btn-fill btn-primary"><i class="fa fa-plus"></i> <?php echo trans('lang.add_data'); ?></button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body ">
                        <div id="checkoutsuccess" class="display-none alert alert-success"><?php echo trans('lang.data_checkout_issued'); ?></div>
                        <div id="checkinsuccess" class="display-none alert alert-success"><?php echo trans('lang.data_checkin_succeess'); ?></div>
                        <div id="messagesuccess" class="display-none alert alert-success"><?php echo trans('lang.data_added'); ?></div>
                        <div id="messagedelete" class="display-none alert alert-success"><?php echo trans('lang.data_deleted'); ?></div>
                        <div id="messageupdate" class="display-none alert alert-success"><?php echo trans('lang.data_updated'); ?></div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>


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

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.name'); ?></label>
                                <input name="name" type="text" id="name" class=" form-control" required placeholder="<?php echo trans('lang.name'); ?>" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.serial'); ?></label>
                                <input name="serial" type="text" id="serial" class="form-control " required placeholder="<?php echo trans('lang.serial'); ?>" />
                            </div>
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.quantity'); ?></label>
                                <input name="quantity" type="text" id="quantity" class="form-control " required placeholder="<?php echo trans('lang.quantity'); ?>" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.supplier'); ?></label>
                                <select name="supplierid" id="supplierid" required class="select2 selectCreate">
                                    <option value=""></option>
                                </select>
                            </div>
                            <!-- <div class="form-group col-md-6">
                                <label><?php echo trans('lang.supplier'); ?></label>
                                <select name="supplierid" id="supplierid" required class="form-control">
                                    <option value=""><?php echo trans('lang.supplier'); ?></option>
                                </select>
                            </div> -->

                            <!-- <div class="form-group col-md-6">
                                <label><?php echo trans('lang.assettype'); ?></label>
                                <select name="typeid" id="typeid" required class="form-control">
                                    <option value=""><?php echo trans('lang.assettype'); ?></option>
                                </select>
                            </div> -->
                            <div class="form-group col-md-6">
                                <label>Type</label>
                                <select name="typeid" id="typeid" required class="select2 selectCreate">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Department/Office</label>
                                <select name="locationid" id="locationid" required class="select2 selectCreate">
                                    <option value=""></option>
                                </select>
                            </div>
                            <!-- <div class="form-group col-md-6">
                                <label><?php echo trans('lang.location'); ?></label>
                                <select name="locationid" id="locationid" required class="form-control">
                                    <option value=""><?php echo trans('lang.location'); ?></option>
                                </select>
                            </div> -->
                            <div class="form-group col-md-6">
                                <label>Brand</label>
                                <select name="brandid" id="brandid" required class="select2 selectCreate">
                                    <option value=""></option>
                                </select>
                            </div>
                            <!-- <div class="form-group col-md-6">
                                <label><?php echo trans('lang.brand'); ?></label>
                                <select name="brandid" id="brandid" required class="form-control">
                                    <option value=""><?php echo trans('lang.brand'); ?></option>
                                </select>
                            </div> -->

                        </div>

                        <div class="form-row">
                            <!-- <div class="form-group col-md-6">
                                <label>Unit</label>
                                <select name="unit" id="unit" required class="form-control">
                                    <option value="">Unit</option>
                                </select>
                            </div> -->
                            <div class="form-group col-md-6">
                                <label>Unit</label>
                                <select name="unit" id="unit" required class="select2 selectCreate">
                                    <option value=""></option>
                                </select>
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
                            <!-- <div class="form-group col-md-6 mb-0">
                                <label for="cost" class="control-label"></label>
                                <div class="input-group mb-0">
                                    <span class="input-group-addon setcurrency border-1" id="currency"></span>
                                    <input class="form-control number" required="" placeholder="" id="cost" name="cost" type="text">
                                </div>
                                <label class="error" for="cost"></label>
                            </div> -->

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
                                    <option value="1"><?php echo trans('lang.readytodeploy'); ?></option>
                                    <option value="2"><?php echo trans('lang.pending'); ?></option>
                                    <option value="3"><?php echo trans('lang.archived'); ?></option>
                                    <option value="4"><?php echo trans('lang.broken'); ?></option>
                                    <option value="5"><?php echo trans('lang.lost'); ?></option>
                                    <option value="6"><?php echo trans('lang.outofrepair'); ?></option>
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

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.name'); ?></label>
                                <input name="name" type="text" id="editname" class=" form-control" required placeholder="<?php echo trans('lang.name'); ?>" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.serial'); ?></label>
                                <input name="serial" type="text" id="editserial" class="form-control " required placeholder="<?php echo trans('lang.serial'); ?>" />
                            </div>
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.quantity'); ?></label>
                                <input name="quantity" type="text" id="editquantity" class="form-control " required placeholder="<?php echo trans('lang.quantity'); ?>" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.supplier'); ?></label>
                                <select name="supplierid" id="editsupplierid" required class="form-control">
                                    <option value=""><?php echo trans('lang.supplier'); ?></option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.assettype'); ?></label>
                                <select name="typeid" id="edittypeid" required class="form-control">
                                    <option value=""><?php echo trans('lang.assettype'); ?></option>
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
                            <div class="form-group col-md-6">
                                <label>Unit</label>
                                <select name="editunit" id="editunit" required class="form-control">
                                    <option value="">Please select unit</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6 mb-0">
                                <label for="purchasedate" class="control-label"><?php echo trans('lang.purchasedate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.purchasedate'); ?>" id="editpurchasedate" name="purchasedate" type="text">
                                    <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="purchasedate"></label>
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- <div class="form-group col-md-6 mb-0">
                                <label for="cost" class="control-label"></label>
                                <div class="input-group mb-0">
                                    <span class="input-group-addon setcurrency border-1" id="currency"></span>
                                    <input class="form-control number" required="" placeholder="" id="editcost" name="cost" type="text">
                                </div>
                                <label class="error" for="cost"></label>
                            </div> --}}
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
                                    <option value="1"><?php echo trans('lang.readytodeploy'); ?></option>
                                    <option value="2"><?php echo trans('lang.pending'); ?></option>
                                    <option value="3"><?php echo trans('lang.archived'); ?></option>
                                    <option value="4"><?php echo trans('lang.broken'); ?></option>
                                    <option value="5"><?php echo trans('lang.lost'); ?></option>
                                    <option value="6"><?php echo trans('lang.outofrepair'); ?></option>
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

                        <h5 class="modal-title"><?php echo trans('lang.issue'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="checkoutfailed" class=" display-none alert alert-success"><?php echo trans('lang.data_checkout_failed_quantity'); ?></div>
                        <div class="form-row">

                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.component'); ?></label>
                                <input name="component" type="text" readonly id="checkoutname" class=" form-control" required placeholder="<?php echo trans('lang.component'); ?>" />
                            </div>

                        </div>
                        <!-- removed -->
                        <!-- <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.checkoutto'); ?></label>
                                <select name="assetid" id="checkoutassetid" required class="form-control">
                                    <option value=""><?php echo trans('lang.asset'); ?></option> 
                                </select>
                            </div>
                        </div> -->

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.issueto'); ?></label>
                                <select name="employeeid" id="employeeid" required class="form-control">
                                    <option value=""><?php echo trans('lang.issueto'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Contact Number</label>
                                <input name="contactno" type="text" id="contactno" class="form-control " required placeholder="Contact Number" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Type of I.D</label>
                                <input name="typeofid" type="text" id="typeofid" class="form-control " required placeholder="Type of I.D" />
                            </div>
                        </div>

                        <!-- <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>I.D Number</label>
                                <input name="idno" type="text" id="idno" class="form-control " required placeholder="I.D Number" />
                            </div>
                        </div> -->

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Department/Office</label>
                                <input name="office" type="text" id="office" class="form-control " required placeholder="Department/Office" />
                            </div>
                        </div>


                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.quantity'); ?></label>
                                <input name="quantity" type="text" id="checkoutquantity" class=" form-control" required placeholder="<?php echo trans('lang.quantity'); ?>" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label>Date and Time</label>
                                <div class="input-group mb-0">
                                    <input class="form-control" readonly required placeholder="Please select date"
                                        id="checkoutdate2" name="checkoutdate2" type="datetime-local">
                                    <span class="input-group-addon border-1" id="date"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label for="checkoutdate" class="control-label"><?php echo trans('lang.checkoutdate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.checkoutdate'); ?>" id="checkoutdate" name="checkoutdate" type="text">
                                    <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="checkoutdate"></label>
                            </div>
                        </div> -->

                        <div class="form-group">
                            <label><?php echo trans('lang.controlno'); ?></label>
                            <input class="form-control" name="controlno" id="controlno" placeholder="<?php echo trans('lang.controlno'); ?>"></input>
                        </div>

                        <div class="form-row ">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.issuancetype'); ?></label>
                                <select name="issuancetype" id="issuancetype" required class="form-control">
                                    <option value=""><?php echo trans('lang.issuancetype'); ?></option>
                                    <option value="1"><?php echo trans('lang.mr'); ?></option>
                                    <option value="2"><?php echo trans('lang.dod'); ?></option>
                                    <option value="3"><?php echo trans('lang.issuanceform'); ?></option>
                                </select>
                            </div>
                        </div>


                        <!-- <div class="form-group">
                            <label><?php echo trans('lang.receivedby'); ?></label>
                            <textarea class="form-control" name="receivedby" id="receivedby" placeholder="<?php echo trans('lang.receivedby'); ?>"></textarea>
                        </div> -->
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Issued By</label>
                                <input class="form-control" name="receivedby" id="receivedby" readonly></input>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.remarks'); ?></label>
                            <textarea class="form-control" name="remarks2" id="remarks2" placeholder="<?php echo trans('lang.remarkshere'); ?>"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="componentid" id="checkoutcomponentid" />
                        <button type="submit" class="btn btn-primary" id="savecheckout"><?php echo trans('lang.save'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!--add checkout -->

    <!--end checkout-->
    <div id="batchscanning" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">


                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo trans('lang.scan_data'); ?> (Batch)</h5>
                        <button type="button" class="reloaddata ml-3 badge badge-data text-white background-green">
                            Reload
                        </button>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <!-- ALERTS -->
                        <div id="scansuccess" class="display-none alert alert-success">
                            Data successfully saved.
                        </div>
                        <div id="scanfail" class="display-none alert alert-danger">
                            Error saving data.
                        </div>

                        <!-- SCAN INPUT -->
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Scan Item</label>
                                <input name="search" type="text" id="scansearchbatch"
                                    class="form-control"
                                    required
                                    placeholder="Scan Asset Tag / Serial..." />
                            </div>
                        </div>

                <form action="#" id="batchformscanning" enctype="multipart/form-data" autocomplete="off">


                        <!-- SCANNED LIST TABLE -->
                        <div class="form-group mt-3">
                            <label>Scanned Items</label>

                            <table class="table table-bordered" id="scannedBatchTable">
                                <thead>
                                    <tr>
                                        <th>Asset Tag</th>
                                        <th>Asset Name</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- ASSIGN TO -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.issueto'); ?></label>
                                <select name="checkoutemployeeid1" id="checkoutemployeeid1"
                                    required class="form-control">
                                    <option value=""><?php echo trans('lang.issueto'); ?></option>
                                </select>
                            </div>

                            <!-- <div class="form-group col-md-6">
                                <label>Department / Office Representing</label>
                                <select name="depid" id="depid"
                                    required class="form-control">
                                    <option value="">Select Department</option>
                                </select>
                            </div> -->

                            <div class="form-group col-md-6">
                                <label>Department / Office Representing</label>
                                <select name="depid" id="depid" required class="select2 selectCreate">
                                    <option value=""></option>
                                </select>
                                <!-- <input class="form-control" name="depid" id="depid" placeholder="Department / Office Representing"></input> -->
                            </div>
                        </div>

                        <!-- CONTROL / DATE -->
                        <div class="form-row">

                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.controlno'); ?></label>
                                <input class="form-control"
                                    name="controlno"
                                    id="controlno_batch"
                                    placeholder="<?php echo trans('lang.controlno'); ?>">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Issuance Date & Time</label>
                                <input class="form-control"
                                    readonly
                                    required
                                    id="checkindate"
                                    name="checkindate"
                                    type="datetime-local">
                            </div>

                        </div>

                        <!-- CONDITION / PURPOSE -->
                        <div class="form-row">

                            <div class="form-group col-md-6">
                                <label>Condition of Equipment</label>
                                <select name="core" id="core"
                                    required class="form-control">
                                    <option value=""></option>
                                    <option value="Serviceable">Serviceable</option>
                                    <option value="Under Maintenance">Under Maintenance</option>
                                    <option value="For Repair">For Repair</option>
                                    <option value="For Upgrade">For Upgrade</option>
                                </select>
                            </div>

                         
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.issuancetype'); ?></label>
                                <select name="issuancetype1" id="issuancetype1" required class="form-control">
                                    <option value="" disabled><?php echo trans('lang.issuancetype'); ?></option>
                                    <!-- <option value="1"><?php echo trans('lang.mr'); ?></option>
                                    <option value="2"><?php echo trans('lang.dod'); ?></option> -->
                                    <option value="3"><?php echo trans('lang.issuanceform'); ?></option>
                                </select>
                            </div>

                        </div>

                        <!-- ISSUED BY -->
                        <!-- <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Issued By</label>
                                <input class="form-control"
                                    name="receivedby1"
                                    id="receivedby1"
                                    readonly>
                            </div>
                        </div> -->

                        <!-- REMARKS -->
                        <div class="form-group">
                            <label><?php echo trans('lang.remarks'); ?></label>
                            <textarea class="form-control"
                                name="remarks"
                                id="remarks"
                                placeholder="<?php echo trans('lang.remarkshere'); ?>"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit"
                            class="btn btn-success"
                            id="saveBatchScanning">
                            Save All Scans
                        </button>

                        <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                            <?php echo trans('lang.close'); ?>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- batch issuance  -->
    <div id="batchcheckout" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <form action="#" id="batchformcheckout" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-header">

                        <h5 class="modal-title">Batch <?php echo trans('lang.issue'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="checkoutfailed1" class="display-none alert alert-success">No Stock Available</div>
                        <div id="checkoutsuccess1" class="display-none alert alert-success"><?php echo trans('lang.data_checkout_issued'); ?></div>

                        <div class="form-row">


                            <div class="form-group col-md-12">
                                <label>Consumables</label>
                                <select name="component1" id="checkoutname1" required class="form-control">
                                    <option value="">Consumables</option>
                                </select>
                                <!-- <input name="component1" type="text" id="checkoutname1" class=" form-control" required placeholder="<?php echo trans('lang.component'); ?>" /> -->
                            </div>

                        </div>

                        <div class="form-group mt-3">
                            <label>Available Items</label>
                            <div class="form-group">
                                <label for="componentSearch">Search by Serial</label>
                                <input type="text" id="componentSearch" class="form-control" placeholder="Enter serial number...">
                            </div>
                            <table class="table table-bordered" id="componentListTable">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Serial</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.serial'); ?></label>
                                <input name="serial1" type="text" id="serial1" class="form-control " required placeholder="<?php echo trans('lang.serial'); ?>" />
                            </div>
                        </div> -->
                        <!-- removed -->
                        <!-- <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.checkoutto'); ?></label>
                                <select name="assetid" id="checkoutassetid" required class="form-control">
                                    <option value=""><?php echo trans('lang.asset'); ?></option> 
                                </select>
                            </div>
                        </div> -->

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.issueto'); ?></label>
                                <select name="employeeid1" id="employeeid1" required class="form-control">
                                    <option value=""><?php echo trans('lang.issueto'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Contact Number</label>
                                <input name="contactno" type="text" id="contactno" class="form-control " required placeholder="Contact Number" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Type of I.D</label>
                                <input name="typeofid" type="text" id="typeofid" class="form-control " required placeholder="Type of I.D" />
                            </div>
                        </div>

                        <!-- <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>I.D Number</label>
                                <input name="idno" type="text" id="idno" class="form-control " required placeholder="I.D Number" />
                            </div>
                        </div> -->

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Department/Office</label>
                                <input name="office" type="text" id="office" class="form-control " required placeholder="Department/Office" />
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label><?php echo trans('lang.quantity'); ?></label>
                            <input name="quantity" type="text" id="quantity" class="form-control " required placeholder="<?php echo trans('lang.quantity'); ?>" />
                        </div>

                        <!-- <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label for="checkoutdate" class="control-label"><?php echo trans('lang.checkoutdate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.checkoutdate'); ?>" id="checkoutdate1" name="checkoutdate1" type="text">
                                    <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="checkoutdate1"></label>
                            </div>
                        </div> -->

                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label>Issuance Date & Time</label>
                                <div class="input-group mb-0">
                                    <input class="form-control" readonly required placeholder="Please select date"
                                        id="checkoutdate1" name="checkoutdate1" type="datetime-local">
                                    <span class="input-group-addon border-1" id="date"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label><?php echo trans('lang.controlno'); ?></label>
                            <input class="form-control" name="controlno1" id="controlno1" placeholder="<?php echo trans('lang.controlno'); ?>"></input>
                        </div>

                        <div class="form-row ">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.issuancetype'); ?></label>
                                <select name="issuancetype1" id="issuancetype1" required class="form-control">
                                    <option value="" disabled><?php echo trans('lang.issuancetype'); ?></option>
                                    <!-- <option value="1"><?php echo trans('lang.mr'); ?></option>
                                    <option value="2"><?php echo trans('lang.dod'); ?></option> -->
                                    <option value="3"><?php echo trans('lang.issuanceform'); ?></option>
                                </select>
                            </div>
                        </div>



                        <!-- <div class="form-group">
                            <label><?php echo trans('lang.receivedby'); ?></label>
                            <textarea class="form-control" name="receivedby" id="receivedby" placeholder="<?php echo trans('lang.receivedby'); ?>"></textarea>
                        </div> -->
                        <div class="form-row">
                            <div class="col-md-12 mb-0">
                                <label>Issued By</label>
                                <input class="form-control" name="receivedby1" id="receivedby1" readonly></input>
                                <!-- <select name="receivedby1" id="receivedby1" required class="form-control">
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
                        <input type="hidden" name="componentid1" id="checkoutcomponentid1" />
                        <button type="submit" class="btn btn-primary" id="savecheckout1"><?php echo trans('lang.save'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>





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
                        <div class="display-none messageexist alert alert-success"><?php echo trans('lang.errordeletecomponent'); ?></div>
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
    function zeroquantity(quantity, checkstatus) {
        var remainingQty = parseInt(quantity, 10);
        if (isNaN(remainingQty)) {
            remainingQty = 0;
        }

        if (checkstatus == 2 && remainingQty <= 0) {
            return "<span class='badge badge-data text-white background-red'>Issued</span>";
        }

        if (remainingQty > 0) {
            return "<span class='badge badge-data text-white background-green'>Remaining: " + remainingQty + "</span>";
        }

        return "<span class='badge badge-data text-white background-red'>Issued</span>";
    }

    function controlnumber(control_number) {
        // console.log(avalaiblequantity)
        if (control_number == 0 || control_number == "" || control_number == null) {
            return "<span class='badge badge-data text-white background-gray'>Not Available</span>";
        } else {
            return control_number;
        }
    }

    function issuetype(issuancetype) {
        if (issuancetype == 1) {
            return "<?php echo trans('lang.mr'); ?>";
        } else if (issuancetype == 2) {
            return "<?php echo trans('lang.dod'); ?>";

        } else if (issuancetype == 3) {
            return "<?php echo trans('lang.issuanceform'); ?>";

        } else {
            return "";
        }
    }


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

            var formattedDateTime = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;

            // Set the value of the checkindate input field
            $('#checkoutdate1').val(formattedDateTime);
            $('#checkoutdate2').val(formattedDateTime);

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
                } else if (selectedValue === 'category') {
                    var url = "{{ URL::to('categorylist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'checkoutemployeeid1') {
                    var url = "{{ URL::to('employeeslist') }}";
                    window.open(url, '_blank');
                }

            });


        });

        function generateBatchControlNumber() {
            $.ajax({
                type: "GET",
                url: "{{ url('generateComponentControlNumber') }}",
                data: {
                    prefix: "IF"
                },
                dataType: "JSON",
                success: function(response) {
                    if (response && response.success) {
                        $('#controlno_batch').val(response.message);
                    }
                }
            });
        }


        "use strict";

        var table = $('#data').DataTable({
            ajax: {
                url: "{{ url('getGroupedComponents') }}"
            },
            columns: [{
                    data: 'pictures',
                    title: 'Picture',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    title: 'Component Name'
                },
                {
                    data: 'all_serials',
                    visible: false
                },
                {
                    data: 'total',
                    title: 'Total Items'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            buttons: []
        });

        $('#data tbody').on('click', '.btn-show-component', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var name = $(this).data('name');

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                $.get("{{ url('getComponentsByName') }}/" + encodeURIComponent(name), function(data) {
                    var html = '<table class="table table-bordered" style="width:100%">';
                    html += '<thead><tr>';
                    html += '<th>Serial</th>';
                    html += '<th>Supplier</th>';
                    html += '<th>Brand</th>';
                    html += '<th>Location</th>';
                    html += '<th>Type</th>';
                    html += '<th>Quantity</th>';
                    html += '<th>Available Quantity</th>';
                    html += '<th>Control No.</th>';
                    html += '<th>Issue Type</th>';
                    html += '<th>Action</th>';
                    html += '</tr></thead><tbody>';

                    data.forEach(function(item) {
                        html += '<tr>';
                        html += '<td>' + (item.serial ?? '-') + '</td>';
                        html += '<td>' + (item.supplier ?? '-') + '</td>';
                        html += '<td>' + (item.brand ?? '-') + '</td>';
                        html += '<td>' + (item.location ?? '-') + '</td>';
                        html += '<td>' + (item.type ?? '-') + '</td>';
                        html += '<td>' + (item.quantity ?? '-') + '</td>';
                        html += '<td>' + zeroquantity(item.caquantity, item.checkstatus) + '</td>';
                        html += '<td>' + (item.control_number ?? '-') + '</td>';
                        html += '<td>' + issuetype(item.issuancetype ?? '-') + '</td>';
                        html += '<td>' + item.action + '</td>';
                        html += '</tr>';
                    });

                    html += '</tbody></table>';

                    row.child(html).show();
                    tr.addClass('shown');
                });
            }
        });

        //get all supplier



        //add data
        $("#formadd").validate({
            rules: {
                quantity: {
                    required: true,
                    digits: true
                },
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
                var serial = $("#serial").val();
                var quantity = $("#quantity").val();
                var purchasedate = $("#purchasedate").val();
                // var cost = $("#cost").val();
                var unit = $("#unit").val();
                var warranty = $("#warranty").val();
                var status = $("#status").val();
                var description = $("#description").val();
                var picture = $('#picture')[0].files[0];

                form.append('name', name);
                form.append('locationid', locationid);
                form.append('supplierid', supplierid);
                form.append('brandid', brandid);
                form.append('typeid', typeid);
                form.append('serial', serial);
                form.append('quantity', quantity);
                form.append('purchasedate', purchasedate);
                // form.append('cost', cost);
                form.append('unit', unit);
                form.append('warranty', warranty);
                form.append('status', status);
                form.append('description', description);
                form.append('picture', picture);

                $.ajax({
                    type: "POST",
                    url: "{{ url('savecomponent')}}",
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
                quantity: {
                    required: true,
                    digits: true
                },
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
                var serial = $("#editserial").val();
                var quantity = $("#editquantity").val();
                var purchasedate = $("#editpurchasedate").val();
                // var cost = $("#editcost").val();
                var unit = $("#editunit").val();
                var warranty = $("#editwarranty").val();
                var status = $("#editstatus").val();
                var description = $("#editdescription").val();
                var picture = $('#editpicture')[0].files[0];

                form.append('name', name);
                form.append('id', id);
                form.append('locationid', locationid);
                form.append('supplierid', supplierid);
                form.append('brandid', brandid);
                form.append('typeid', typeid);
                form.append('serial', serial);
                form.append('quantity', quantity);
                form.append('purchasedate', purchasedate);
                // form.append('cost', cost);
                form.append('unit', unit);
                form.append('warranty', warranty);
                form.append('status', status);
                form.append('description', description);
                form.append('picture', picture);

                $.ajax({
                    type: "POST",
                    url: "{{ url('updatecomponent')}}",
                    data: form,
                    contentType: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
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


        function showreference() {

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

            //get all asset list
            $.ajax({
                type: "GET",
                url: "{{ url('listasset')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#checkoutassetid").append($("<option></option>")
                        .attr("value", "")
                        .text(""));
                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.name);

                        $("#checkoutassetid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                    });
                    $("#checkoutassetid").append($("<option></option>")
                        .attr("value", "checkoutassetid")
                        .text("Add New Data"));
                }
            });


            $.ajax({
                type: "GET",
                url: "{{ url('listemployees')}}",
                dataType: "JSON",
                success: function(html) {
                    var objs = html.message;
                    jQuery.each(objs, function(index, record) {
                        var id = decodeURIComponent(record.id);
                        var name = decodeURIComponent(record.fullname);
                        $("#checkinemployeeid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                        $("#checkoutemployeeid1").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                        $("#checkoutemployeeid").append($("<option></option>")
                            .attr("value", id)
                            .text(name));
                    });
                }
            });

            // get all receiver, logistic custodian
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
                url: "{{ url('listcomponent') }}",
                dataType: "JSON",
                success: function(response) {
                    // Access the 'data' array from the response
                    var objs = response.data;

                    // Clear the dropdown before appending new options
                    $("#checkoutname1").empty();

                    // Iterate through each item in the data array
                    $.each(objs, function(index, item) {
                        var name = decodeURIComponent(item.name); // Decode name if necessary
                        var groupid = decodeURIComponent(item.groupid); // Decode groupid if necessary

                        // Append each option to the dropdown
                        $("#checkoutname1").append($("<option></option>")
                            .attr("value", groupid) // Set the value to groupid
                            .text(name)); // Set the text to name
                    });
                },
                error: function(xhr, status, error) {
                    // Handle any errors that occurred during the request
                    console.error("AJAX Error: ", status, error);
                }
            });


        }

        //delete data
        // $("#formdelete").validate({
        //     submitHandler: function(form) {
        //         $.ajax({
        //             method: "POST",
        //             url: "{{ url('deletecomponent')}}",
        //             data: $("#formdelete").serialize(),
        //             dataType: "JSON",
        //             success: function(data) {
        //                 console.log(data.message);
        //                 if (data.message == 'success') {
        //                     $("#messagedelete").css({
        //                         'display': "block"
        //                     });
        //                     $('#delete').modal('hide');
        //                     window.setTimeout(function() {
        //                         location.reload()
        //                     }, 2000);
        //                 }
        //                 if (data.message == 'exist') {
        //                     $(".messageexist").css({
        //                         'display': "block"
        //                     });
        //                 }


        //             }
        //         });
        //     }
        // });

        // show edit data
        // $('#edit').on('show.bs.modal', function(e) {
        //     var $modal = $(this),
        //         id = $(e.relatedTarget).attr('customdata');
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ url('componentbyid')}}",
        //         data: {
        //             id: id
        //         },
        //         dataType: "JSON",
        //         success: function(data) {
        //             $("#editid").val(id);
        //             $("#editname").val(data.message.componentname);
        //             $("#editlocationid").val(data.message.locationid);
        //             $("#editsupplierid").val(data.message.supplierid);
        //             $("#editbrandid").val(data.message.brandid);
        //             $("#edittypeid").val(data.message.typeid);
        //             $("#editserial").val(data.message.serial);
        //             $("#editquantity").val(data.message.quantity);
        //             $("#editpurchasedate").val(data.message.purchasedate);
        //             $("#editcost").val(data.message.cost);
        //             $("#editwarranty").val(data.message.warranty);
        //             $("#editstatus").val(data.message.status);
        //             $("#editdescription").val(data.message.componentdescription);
        //         }
        //     });
        // });


        var targetModalEvent = null;
        var eventHolder;
        var x;

        function showEditModal() {
            showreference();
            // $("#edit").prop('class', 'modal fade');
            if (targetModalEvent) {
                var $modal = $('#edit'),
                    id = $(targetModalEvent.relatedTarget).attr('customdata');
                $.ajax({
                    type: "POST",
                    url: "{{ url('componentbyid') }}",
                    data: {
                        id: id
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $("#editid").val(id);
                        $("#editname").val(data.message.componentname);
                        $("#editlocationid").val(data.message.locationid);
                        $("#editsupplierid").val(data.message.supplierid);
                        $("#editbrandid").val(data.message.brandid);
                        $("#edittypeid").val(data.message.typeid);
                        $("#editserial").val(data.message.serial);
                        $("#editquantity").val(data.message.quantity);
                        $("#editpurchasedate").val(data.message.purchasedate);
                        // $("#editcost").val(data.message.cost);
                        $("#editunit").val(data.message.unit);
                        $("#editwarranty").val(data.message.warranty);
                        $("#editstatus").val(data.message.status);
                        $("#editdescription").val(data.message.componentdescription);
                    }
                });
                $('#edit').modal('show');
                $("#editcontent").css('display', 'block');
                targetModalEvent = null;
            }
        }

        function showDeleteModal() {
            // $("#edit").prop('class', 'modal fade');
            if (targetModalEvent) {
                var $modal = $(this),
                    id = $(targetModalEvent.relatedTarget).attr('customdata');
                $("#iddelete").val(id);
                $("#formdelete").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            method: "POST",
                            url: "{{ url('deletecomponent')}}",
                            data: $("#formdelete").serialize(),
                            dataType: "JSON",
                            success: function(data) {
                                console.log(data);
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

        $("#edit #delete").on('hide.bs.modal', function() {
            $("#editcontent").css('display', 'none');
        });


        // modals goes here
        // edit data
        $('#edit').on('show.bs.modal', function(e) {
            x = 1;
            // addmodal();
            eventHolder = 'edit';
            // e.preventDefault();
            // $("#edit").css('display', 'none');

            targetModalEvent = e;
            // $("#password").modal("show");
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

        $("#add").on('show.bs.modal', function(e) {
            showreference();
        });
        $("#batchscanning").on('show.bs.modal', function(e) {
            showreference();
            generateBatchControlNumber();
        });


        //get all employee


        $("#checkoutname1").on("change", function() {

            var groupid = $(this).val();

            if (groupid == "") return;

            $.ajax({
                type: "GET",
                url: "{{ url('getcomponentbygroup') }}",
                data: {
                    groupid: groupid
                },
                dataType: "JSON",
                success: function(response) {

                    var tableBody = $("#componentListTable tbody");
                    tableBody.empty();

                    $.each(response.data, function(index, item) {

                        var statusText = item.checkstatus == 0 ? "Available" : "Issued";

                        var row = `
                    <tr>
                        <td>
                            <input type="checkbox" name="component_ids[]" value="${item.id}">
                        </td>
                        <td>${item.serial}</td>
                        <td>${item.quantity}</td>
                        <td>${statusText}</td>
                    </tr>
                `;

                        tableBody.append(row);
                    });
                }
            });
        });



        // $.ajax({
        //     type: "GET",
        //     url: "{{ url('listcomponent') }}",
        //     dataType: "JSON",
        //     success: function(response) {

        //         var objs = response.data;
        //         var tableBody = $("#consumableTable tbody");
        //         tableBody.empty();

        //         $.each(objs, function(index, item) {

        //             var row = `
        //         <tr>
        //             <td>
        //                 <input type="checkbox" name="items[${index}][selected]" value="1">
        //                 <input type="hidden" name="items[${index}][componentid]" value="${item.groupid}">
        //             </td>
        //             <td>${item.name}</td>
        //             <td>${item.quantity}</td>
        //             <td>
        //                 <input type="number" 
        //                        name="items[${index}][quantity]" 
        //                        class="form-control"
        //                        min="1"
        //                        max="${item.quantity}">
        //             </td>
        //         </tr>
        //     `;

        //             tableBody.append(row);
        //         });
        //     }
        // });

        // Filter table rows based on serial input
        $("#componentSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();

            $("#componentListTable tbody tr").filter(function() {
                $(this).toggle($(this).find("td:eq(1)").text().toLowerCase().indexOf(value) > -1);
            });
        });

        //checkout
        $("#formcheckout").validate({
            rules: {
                quantity: {
                    required: true,
                    digits: true
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    method: "POST",
                    url: "{{ url('savecheckoutcomponent')}}",
                    data: $("#formcheckout").serialize(),
                    dataType: "JSON",
                    success: function(data) {
                        //check if more than avalaible quantity
                        console.log(data);
                        if (data.success == '0') {
                            $("#checkoutfailed").css({
                                'display': "block"
                            });
                        }
                        if (data.success == 'success') {
                            $("#checkoutsuccess").css({
                                'display': "block"
                            });
                            $('#checkout').modal('hide');
                            window.setTimeout(function() {
                                location.reload()
                            }, 2000)
                        }
                    }
                });
            }
        });

        $("#batchformcheckout").validate({
            rules: {
                quantity: {
                    required: true,
                    digits: true
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    method: "POST",
                    url: "{{ url('batchsavecheckoutcomponent')}}",
                    data: $("#batchformcheckout").serialize(),
                    dataType: "JSON",
                    success: function(data) {
                        //check if more than avalaible quantity
                        if (data.success == '0') {
                            console.log(data);
                            $("#checkoutfailed1").css({
                                'display': "block"
                            });
                            window.setTimeout(function() {
                                $('#batchcheckout').modal('hide');
                                location.reload()
                            }, 4000)
                        }
                        if (data.success == 'success') {
                            $("#checkoutsuccess1").css({
                                'display': "block"
                            });
                            window.setTimeout(function() {
                                $('#batchcheckout').modal('hide');
                                location.reload()
                            }, 2000)
                        }
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
                url: "{{ url('componentbyid')}}",
                data: {
                    id: id
                },
                dataType: "JSON",
                success: function(data) {
                    console.log(data);
                    $("#checkoutcomponentid").val(id);
                    $("#checkoutname").val(data.message.componentname);
                }
            });
        });

        $('#batchcheckout').on('show.bs.modal', function(e) {
            $("#receivedby1").val(loggedInUserId);
        });

        $('#checkout').on('show.bs.modal', function(e) {
            $("#receivedby").val(loggedInUserId);
        });




        //show delete data

        $('#delete').on('show.bs.modal', function(e) {
            var $modal = $(this),
                id = $(e.relatedTarget).attr('customdata');
            $("#iddelete").val(id);
        });
        let scannedComponents = [];
        let batchScanSearchTimer = null;
        let isBatchLookupInFlight = false;

        function resolveAvailableQuantity(data) {
            const messageData = data && data.message ? data.message : {};
            const fromMessageQty = parseInt(messageData.quantity, 10);
            const fromMessageAvailable = parseInt(messageData.available_quantity, 10);
            const fromRootAvailable = parseInt(data && data.available_quantity, 10);
            const fromRootQty = parseInt(data && data.quantity, 10);
            const resolved = !Number.isNaN(fromMessageAvailable) ? fromMessageAvailable :
                !Number.isNaN(fromMessageQty) ? fromMessageQty :
                !Number.isNaN(fromRootAvailable) ? fromRootAvailable :
                !Number.isNaN(fromRootQty) ? fromRootQty : 0;

            return Math.max(0, resolved);
        }

        function runBatchComponentLookup(showEmptyAlert = false) {
            if (isBatchLookupInFlight) return;

            let searchValue = $('#scansearchbatch').val().trim();
            if (!searchValue) {
                if (showEmptyAlert) {
                    alert("Please enter a valid serial.");
                }
                return;
            }

            isBatchLookupInFlight = true;

            $.ajax({
                type: "POST",
                url: "{{ url('componentBySerial') }}",
                data: {
                    receivedby: loggedInUserId,
                    searchValue: searchValue
                },
                dataType: "JSON",
                success: function(data) {
                    if (data.success === 'success' && data.message) {
                        const assetId = data.message.serial;
                        const dbQuantity = resolveAvailableQuantity(data);

                        if (dbQuantity <= 0) {
                            alert("Component is already issued.");
                            $('#scansearchbatch').val('').focus();
                            return;
                        }

                        const alreadyScanned = scannedComponents.some(
                            item => item.serial === assetId
                        );

                        if (alreadyScanned) {
                            alert("Component already scanned.");
                            $('#scansearchbatch').val('').focus();
                            return;
                        }

                        const isEditableQuantity = dbQuantity > 1;

                        scannedComponents.push({
                            ...data.message,
                            available_quantity: dbQuantity,
                            requested_quantity: 1
                        });

                        $('#scannedBatchTable tbody').append(`
                        <tr data-id="${assetId}">
                            <td>${data.message.serial}</td>
                            <td>${data.message.name}</td>
                            <td>
                                <input
                                    type="number"
                                    class="form-control form-control-sm batch-qty-input"
                                    data-serial="${assetId}"
                                    min="1"
                                    max="${dbQuantity}"
                                    value="1"
                                    ${isEditableQuantity ? '' : 'readonly'}
                                >
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger remove-scan">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    `);

                        $('#scansearchbatch').val('').focus();
                    } else {
                        if (data.message === 'already_issued') {
                            alert("Component is already issued.");
                        } else {
                            alert("Component not available.");
                        }
                    }
                },
                complete: function() {
                    isBatchLookupInFlight = false;
                }
            });
        }

        $('#scansearchbatch').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                if (batchScanSearchTimer) {
                    clearTimeout(batchScanSearchTimer);
                }
                runBatchComponentLookup(true);
            }
        });

        $('#scansearchbatch').on('input', function() {
            if (batchScanSearchTimer) {
                clearTimeout(batchScanSearchTimer);
            }

            // Auto-search after scanner input settles, even without Enter.
            batchScanSearchTimer = setTimeout(function() {
                runBatchComponentLookup(false);
            }, 250);
        });

        $(document).on('click', '.remove-scan', function() {

            let row = $(this).closest('tr');
            let serial = row.data('id');

            scannedComponents = scannedComponents.filter(item =>
                item.serial !== serial
            );

            row.remove();
        });

        $(document).on('input change', '.batch-qty-input', function() {
            const $input = $(this);
            const serial = $input.data('serial');
            const maxQty = parseInt($input.attr('max'), 10) || 1;
            let requestedQty = parseInt($input.val(), 10);

            if (Number.isNaN(requestedQty) || requestedQty < 1) {
                requestedQty = 1;
            } else if (requestedQty > maxQty) {
                requestedQty = maxQty;
            }

            $input.val(requestedQty);

            scannedComponents = scannedComponents.map(item =>
                item.serial === serial ? {
                    ...item,
                    requested_quantity: requestedQty
                } : item
            );
        });

$('#batchformscanning').on('submit', function(e) {
    e.preventDefault();

    if (scannedComponents.length === 0) {
        alert("No components scanned.");
        return;
    }

    let formData = $(this).serializeArray();

    let dataObject = {};
    formData.forEach(function(field) {
        dataObject[field.name] = field.value;
    });

    const qtyErrors = [];
    $('#scannedBatchTable tbody tr').each(function() {
        const $row = $(this);
        const serial = $row.data('id');
        const $qtyInput = $row.find('.batch-qty-input');
        const requestedQty = parseInt($qtyInput.val(), 10);
        const maxQty = parseInt($qtyInput.attr('max'), 10) || 1;

        if (Number.isNaN(requestedQty) || requestedQty < 1 || requestedQty > maxQty) {
            $qtyInput.addClass('is-invalid');
            qtyErrors.push(`${serial}: quantity must be between 1 and ${maxQty}.`);
            return;
        }

        $qtyInput.removeClass('is-invalid');

        scannedComponents = scannedComponents.map(item =>
            item.serial === serial ? {
                ...item,
                requested_quantity: requestedQty
            } : item
        );
    });

    if (qtyErrors.length > 0) {
        alert(qtyErrors.join("\n"));
        return;
    }

    // attach scanned components
    dataObject.components = scannedComponents.map(item => ({
        ...item,
        available_quantity: item.available_quantity || (parseInt(item.quantity, 10) || 1),
        issue_quantity: item.requested_quantity || 1,
        quantity: item.requested_quantity || 1
    }));

    $.ajax({
        type: "POST",
        url: "{{ url('saveBatchComponentCheckout') }}",
        data: dataObject,
        success: function(response) {
            if (response && response.success && response.print_url) {
                window.open(response.print_url, '_blank');
            }
            alert("Batch Issued Successfully");

            scannedComponents = [];
            $('#scannedBatchTable tbody').empty();
            $('#batchformscanning')[0].reset();
        },
        error: function(xhr) {
            var message = "Failed to save batch issuance.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            alert(message);
        }
    });
});

    })(jQuery);
</script>
@endsection
