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
                        <div class="table-responsive">
                            <table id="data" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.picture'); ?></th>
                                        <th><?php echo trans('lang.plateno'); ?></th>
                                        <th><?php echo trans('lang.serial'); ?></th>
                                        <th><?php echo trans('lang.purchasedate'); ?></th>
                                        <th><?php echo trans('lang.cost'); ?></th>
                                        <th><?php echo trans('lang.description'); ?></th>
                                        <th><?php echo trans('lang.vehiclename'); ?></th>
                                        <!-- <th><?php echo trans('lang.type'); ?></th> -->
                                        <th><?php echo trans('lang.brand'); ?></th>

                                        <th><?php echo trans('lang.vehiclecategory'); ?></th>
                                        <th><?php echo trans('lang.yearmodel'); ?></th>
                                        <th><?php echo trans('lang.yearacquired'); ?></th>
                                        <th><?php echo trans('lang.chassis'); ?></th>
                                        <th><?php echo trans('lang.engineno'); ?></th>
                                        <th><?php echo trans('lang.fueltype'); ?></th>
                                        <th><?php echo trans('lang.transmission'); ?></th>


                                        <th><?php echo trans('lang.location'); ?></th>
                                        <th><?php echo trans('lang.status'); ?></th>
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
                                        <th><?php echo trans('lang.vehiclename'); ?></th>
                                        <!-- <th><?php echo trans('lang.type'); ?></th> -->
                                        <th><?php echo trans('lang.brand'); ?></th>
                                        <th><?php echo trans('lang.vehiclecategory'); ?></th>
                                        <th><?php echo trans('lang.yearmodel'); ?></th>
                                        <th><?php echo trans('lang.yearacquired'); ?></th>
                                        <th><?php echo trans('lang.chassis'); ?></th>
                                        <th><?php echo trans('lang.engineno'); ?></th>
                                        <th><?php echo trans('lang.fueltype'); ?></th>
                                        <th><?php echo trans('lang.transmission'); ?></th>
                                        <th><?php echo trans('lang.location'); ?></th>
                                        <th><?php echo trans('lang.status'); ?></th>
                                        <th><?php echo trans('lang.hstatus'); ?></th>
                                        <th><?php echo trans('lang.action'); ?></th>
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
                        <div class="display-none messageexist alert alert-success"><?php echo trans('lang.tag_exist'); ?></div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.vehiclename'); ?></label>
                                <input name="name" type="text" id="name" class=" form-control" required placeholder="<?php echo trans('lang.vehiclename'); ?>" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.plateno'); ?></label>
                                <input name="assettag" type="text" id="assettag" class=" form-control" required placeholder="<?php echo trans('lang.plateno'); ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.supplier'); ?></label>
                                <select name="supplierid" id="supplierid" required class="select2 selectCreate">
                                    <option value="" selected disabled><?php echo trans('lang.supplier'); ?></option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.location'); ?></label>
                                <select name="locationid" id="locationid" required class="select2 selectCreate">
                                    <option value="" selected disabled><?php echo trans('lang.location'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.brand'); ?></label>
                                <select name="brandid" id="brandid" required class="select2 selectCreate">
                                    <option value="" selected disabled><?php echo trans('lang.brand'); ?></option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.assettype'); ?></label>
                                <select name="typeid" id="typeid" required class="form-control">
                                    <option value="7" selected>Vehicle</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.vehiclecategory'); ?></label>
                                <select name="vehiclecategory" id="vehiclecategory" required class="form-control">
                                    <option value="" selected disabled><?php echo trans('lang.vehiclecategory'); ?></option>
                                    <option value="Motorcycle">Motorcycle</option>
                                    <option value="Van">Van</option>
                                    <option value="SUV">SUV</option>
                                    <option value="MPV">MPV</option>
                                    <option value="Sedan">Sedan</option>
                                    <option value="Truck">Truck</option>
                                    <option value="Busses / Coaster">Busses / Coaster</option>
                                    <option value="Other">Other</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- <div class="form-group col-md-4">
                                <label><?php echo trans('lang.vehicledesc'); ?></label>
                                <input name="vehicledesc" type="text" id="vehicledesc" class="form-control " required placeholder="<?php echo trans('lang.vehicledesc'); ?>" />
                            </div> -->
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.yearmodel'); ?></label>
                                <input name="yearmodel" type="text" id="yearmodel" class="form-control " required placeholder="<?php echo trans('lang.yearmodel'); ?>" />
                            </div>
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.yearacquired'); ?></label>
                                <input name="yearacquired" type="text" id="yearacquired" class="form-control " required placeholder="<?php echo trans('lang.yearacquired'); ?>" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.chassis'); ?></label>
                                <input name="chassis" type="text" id="chassis" class="form-control " required placeholder="<?php echo trans('lang.chassis'); ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.engineno'); ?></label>
                                <input name="engineno" type="text" id="engineno" class="form-control " required placeholder="<?php echo trans('lang.engineno'); ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.fueltype'); ?></label>
                                <select name="fueltype" id="fueltype" required class="form-control">
                                    <option value="" selected disabled><?php echo trans('lang.fueltype'); ?></option>
                                    <option value="Gasoline">Gasoline</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="Electric">Electric</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.transmission'); ?></label>
                                <select name="transmission" id="transmission" required class="form-control">
                                    <option value="" selected disabled><?php echo trans('lang.transmission'); ?></option>
                                    <option value="Manual">Manual</option>
                                    <option value="Automatic">Automatic</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 mb-0">
                                <label for="cost" class="control-label"><?php echo trans('lang.cost'); ?></label>
                                <div class="input-group mb-0">
                                    <span class="input-group-addon setcurrency border-1" id="currency"></span>
                                    <input class="form-control number" required="" placeholder="<?php echo trans('lang.cost'); ?>" id="cost" name="cost" type="text">
                                </div>
                                <label class="error" for="cost"></label>
                            </div>
                            <div class="form-group col-md-4 mb-0">
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
                                    <option value="" selected disabled><?php echo trans('lang.status'); ?></option>
                                    <option value="1"><?php echo trans('lang.readytodeploy'); ?></option>
                                    <option value="2"><?php echo trans('lang.pending'); ?></option>
                                    <option value="3"><?php echo trans('lang.archived'); ?></option>
                                    <option value="4"><?php echo trans('lang.broken'); ?></option>
                                    <option value="5"><?php echo trans('lang.lost'); ?></option>
                                    <option value="6"><?php echo trans('lang.unserviceable'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.vehicledesc'); ?></label>
                            <textarea class="form-control" name="description" id="description" placeholder="<?php echo trans('lang.vehicledesc'); ?>"></textarea>
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
            <div class="modal-content display-none  " id="editcontent">

                <form action="#" id="formedit" enctype="multipart/form-data">
                    <div class="modal-header">

                        <h5 class="modal-title"><?php echo trans('lang.edit_data'); ?></h5>
                        <button type="button" class="reloaddata ml-3 badge badge-data text-white background-green">Reload</button>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="display-none messageexist alert alert-success"><?php echo trans('lang.tag_exist'); ?></div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.name'); ?></label>
                                <input name="name" type="text" id="editname" class=" form-control" required placeholder="<?php echo trans('lang.name'); ?>" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.plateno'); ?></label>
                                <input name="assettag" type="text" id="editassettag" class=" form-control" required placeholder="<?php echo trans('lang.plateno'); ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.supplier'); ?></label>
                                <select name="supplierid" id="editsupplierid" required class="form-control">
                                    <option value="" selected disabled><?php echo trans('lang.supplier'); ?></option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.location'); ?></label>
                                <select name="locationid" id="editlocationid" required class="form-control">
                                    <option value="" selected disabled><?php echo trans('lang.location'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.brand'); ?></label>
                                <select name="brandid" id="editbrandid" required class="form-control">
                                    <option value="" selected disabled><?php echo trans('lang.brand'); ?></option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.assettype'); ?></label>
                                <select name="typeid" id="edittypeid" required class="form-control">
                                    <option value="7" selected>Vehicle</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.vehiclecategory'); ?></label>
                                <select name="vehiclecategory" id="editvehiclecategory" required class="form-control">
                                    <option value="" selected disabled><?php echo trans('lang.vehiclecategory'); ?></option>
                                    <option value="Motorcycle">Motorcycle</option>
                                    <option value="Van">Van</option>
                                    <option value="SUV">SUV</option>
                                    <option value="MPV">MPV</option>
                                    <option value="Sedan">Sedan</option>
                                    <option value="Truck">Truck</option>
                                    <option value="Busses / Coaster">Busses / Coaster</option>
                                    <option value="Other">Other</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- <div class="form-group col-md-4">
                                <label><?php echo trans('lang.vehicledesc'); ?></label>
                                <input name="vehicledesc" type="text" id="vehicledesc" class="form-control " required placeholder="<?php echo trans('lang.vehicledesc'); ?>" />
                            </div> -->
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.yearmodel'); ?></label>
                                <input name="yearmodel" type="text" id="edityearmodel" class="form-control " required placeholder="<?php echo trans('lang.yearmodel'); ?>" />
                            </div>
                            <div class="form-group col-md-6">
                                <label><?php echo trans('lang.yearacquired'); ?></label>
                                <input name="yearacquired" type="text" id="edityearacquired" class="form-control " required placeholder="<?php echo trans('lang.yearacquired'); ?>" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.chassis'); ?></label>
                                <input name="chassis" type="text" id="editchassis" class="form-control " required placeholder="<?php echo trans('lang.chassis'); ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.engineno'); ?></label>
                                <input name="engineno" type="text" id="editengineno" class="form-control " required placeholder="<?php echo trans('lang.engineno'); ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.fueltype'); ?></label>
                                <select name="fueltype" id="editfueltype" required class="form-control">
                                    <option value="" selected disabled><?php echo trans('lang.fueltype'); ?></option>
                                    <option value="Gasoline">Gasoline</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="Electric">Electric</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label><?php echo trans('lang.transmission'); ?></label>
                                <select name="transmission" id="edittransmission" required class="form-control">
                                    <option value="" selected disabled><?php echo trans('lang.transmission'); ?></option>
                                    <option value="Manual">Manual</option>
                                    <option value="Automatic">Automatic</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 mb-0">
                                <label for="cost" class="control-label"><?php echo trans('lang.cost'); ?></label>
                                <div class="input-group mb-0">
                                    <span class="input-group-addon setcurrency border-1" id="currency"></span>
                                    <input class="form-control number" required="" placeholder="<?php echo trans('lang.cost'); ?>" id="editcost" name="cost" type="text">
                                </div>
                                <label class="error" for="cost"></label>
                            </div>
                            <div class="form-group col-md-4 mb-0">
                                <label for="purchasedate" class="control-label"><?php echo trans('lang.purchasedate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.purchasedate'); ?>" id="editpurchasedate" name="purchasedate" type="text">
                                    <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="purchasedate"></label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6 mb-0">
                                <label for="warranty" class="control-label"><?php echo trans('lang.warranty'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control number" required="" placeholder="<?php echo trans('lang.warranty'); ?>" id="editwarranty" name="warranty" type="text">
                                    <span class="input-group-addon border-1" id="warrantyyear"><?php echo trans('lang.month'); ?></span>
                                </div>
                                <label class="error" for="warranty"></label>
                            </div>
                            <div class="form-group col-md-6 mb-0">
                                <label><?php echo trans('lang.status'); ?></label>
                                <select name="status" id="editstatus" required class="form-control">
                                    <option value="" selected disabled><?php echo trans('lang.status'); ?></option>
                                    <option value="1"><?php echo trans('lang.readytodeploy'); ?></option>
                                    <option value="2"><?php echo trans('lang.pending'); ?></option>
                                    <option value="3"><?php echo trans('lang.archived'); ?></option>
                                    <option value="4"><?php echo trans('lang.broken'); ?></option>
                                    <option value="5"><?php echo trans('lang.lost'); ?></option>
                                    <option value="6"><?php echo trans('lang.unserviceable'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.vehicledesc'); ?></label>
                            <textarea class="form-control" name="description" id="editdescription" placeholder="<?php echo trans('lang.vehicledesc'); ?>"></textarea>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="#" id="formcheckout" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-header">

                        <h5 class="modal-title"><?php echo trans('lang.checkout'); ?></h5>
                        <button type="button" class="reloaddata ml-3 badge badge-data text-white background-green">Reload</button>

                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Plate Number</label>
                                <input name="assettag" type="text" readonly id="checkoutassettag" class=" form-control" required placeholder="<?php echo trans('lang.assettag'); ?>" />
                            </div>
                            <div class="form-group col-md-12">
                                <label>Asset Vehicle</label>
                                <input name="asset" type="text" readonly id="checkoutname" class=" form-control" required placeholder="Asset Vehicle" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label id="borrowername">Logistic Custodian</label>
                                <input type="text" class="form-control" readonly value="{{ Auth::user()->fullname }}" />
                                <input type="hidden" name="employeeid" id="checkoutemployeeid" value="0" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.hstatus'); ?></label>
                                <select name="vehiclestatus" id="checkoutvehiclestatus" required class="form-control">
                                    <option value="borrowed" selected><?php echo trans('lang.checkout'); ?></option>
                                    <option value="returned"><?php echo trans('lang.checkin'); ?></option>
                                    <option value="serviceable"><?php echo trans('lang.serviceable'); ?></option>
                                    <option value="unserviceable"><?php echo trans('lang.unserviceable'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div id="checkouttriprow">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Control #</label>
                                    <input name="controlno" type="text" id="checkoutcontrolno" class="form-control" readonly placeholder="Control Number" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Name of Driver</label>
                                    <input name="name_of_driver" type="text" id="checkoutdriver" class="form-control" required placeholder="Name of Driver" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Phone #</label>
                                    <input name="phone" type="text" id="checkoutphone" class="form-control number-only" inputmode="numeric" required placeholder="Phone #" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Destination(s)</label>
                                    <input name="destination" type="text" id="checkoutdestination" class="form-control" required placeholder="Destination(s)" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Purpose(s)</label>
                                    <input name="purpose" type="text" id="checkoutpurpose" class="form-control" required placeholder="Purpose(s)" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Date Borrowed</label>
                                    <input name="date_borrowed" type="text" readonly id="checkoutdateborrowed" class="form-control" placeholder="Date Borrowed" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Time of Departure</label>
                                    <input name="time_of_departure" type="text" readonly id="checkoutdeparture" class="form-control" placeholder="Time of Departure" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Departure Mileage</label>
                                    <input name="departure_mileage" type="text" id="checkoutmileage" class="form-control number-only" inputmode="numeric" required placeholder="Departure Mileage" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Division</label>
                                    <div class="input-group">
                                        <input type="text" id="checkoutdivisioninput" class="form-control" placeholder="Division" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary" id="addcheckoutdivision">Add</button>
                                        </span>
                                    </div>
                                    <div id="checkoutdivisionlist" class="border rounded p-2 mt-2 text-muted" style="min-height: 38px; max-height: 190px; overflow-y: auto; font-size: 14px; line-height: 1.8;">No division added</div>
                                    <input name="division_1" type="hidden" id="checkoutdivision1" />
                                    <input name="division_2" type="hidden" id="checkoutdivision2" />
                                    <input name="division_3" type="hidden" id="checkoutdivision3" />
                                    <input name="divisions_json" type="hidden" id="checkoutdivisionsjson" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Passenger</label>
                                    <div class="input-group">
                                        <input type="text" id="checkoutpassengerinput" class="form-control" placeholder="Passenger" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary" id="addcheckoutpassenger">Add</button>
                                        </span>
                                    </div>
                                    <div id="checkoutpassengerlist" class="border rounded p-2 mt-2 text-muted" style="min-height: 38px; max-height: 190px; overflow-y: auto; font-size: 14px; line-height: 1.8;">No passenger added</div>
                                    <input name="passenger_1" type="hidden" id="checkoutpassenger1" />
                                    <input name="passenger_2" type="hidden" id="checkoutpassenger2" />
                                    <input name="passenger_3" type="hidden" id="checkoutpassenger3" />
                                    <input name="passengers_json" type="hidden" id="checkoutpassengersjson" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Borrower Name</label>
                                    <input name="borrower_signature_name" type="text" id="checkoutsignature" class="form-control" required placeholder="Borrower Name" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Supervising Officer</label>
                                    <input name="supervising_officer" type="text" id="checkoutsupervising" class="form-control" required placeholder="Supervising Officer" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Remarks</label>
                                    <textarea name="trip_remarks" id="checkouttripremarks" class="form-control" rows="5" placeholder="Remarks"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-row display-none" id="checkoutremarksrow">
                            <div class="form-group col-md-12">
                                <label>Remarks</label>
                                <textarea name="remarks" id="checkoutremarks" class="form-control" rows="4" placeholder="Type why this vehicle is unserviceable"></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label for="checkoutdate" class="control-label"><?php echo trans('lang.checkoutdate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" readonly required="" placeholder="<?php echo trans('lang.checkoutdate'); ?>" id="checkoutdate" name="checkoutdate" type="text">
                                    <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="checkoutdate"></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="assetid" id="assetid" />
                        <button type="submit" class="btn btn-primary" id="savecheckout"><?php echo trans('lang.save'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end checkout-->


    <!--add checkin -->
    <div id="checkin" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="#" id="formcheckin" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo trans('lang.checkin'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Plate Number</label>
                                <input name="assettag" type="text" readonly id="checkinassettag" class=" form-control" required placeholder="<?php echo trans('lang.assettag'); ?>" />
                            </div>
                            <div class="form-group col-md-12">
                                <label>Asset Vehicle</label>
                                <input name="asset" type="text" readonly id="checkinname" class=" form-control" required placeholder="Asset Vehicle" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label id="returnname">Logistic Custodian</label>
                                <input type="text" class="form-control" readonly value="{{ Auth::user()->fullname }}" />
                                <input type="hidden" name="employeeid1" id="checkoutemployeeid1" value="0" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.hstatus'); ?></label>
                                <select name="vehiclestatus" id="checkinvehiclestatus" required class="form-control">
                                    <option value="borrowed"><?php echo trans('lang.checkout'); ?></option>
                                    <option value="returned" selected><?php echo trans('lang.checkin'); ?></option>
                                    <option value="serviceable"><?php echo trans('lang.serviceable'); ?></option>
                                    <option value="unserviceable"><?php echo trans('lang.unserviceable'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div id="checkintriprow">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Control #</label>
                                    <input type="text" id="checkincontrolno" class="form-control" readonly placeholder="Control Number" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Date Return</label>
                                    <input name="date_return" type="text" readonly id="checkindatereturn" class="form-control" placeholder="Date Return" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Time of Arrival</label>
                                    <input name="time_of_arrival" type="text" readonly id="checkinarrival" class="form-control" placeholder="Time of Arrival" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Returning Name</label>
                                    <input name="returning_signature_name" type="text" id="checkinreturningname" class="form-control" required placeholder="Returning Name" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Arrival Mileage</label>
                                    <input name="arrival_mileage" type="text" id="checkinmileage" class="form-control number-only" inputmode="numeric" required placeholder="Arrival Mileage" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Remarks</label>
                                    <textarea name="return_remarks" id="checkinreturnremarks" class="form-control" rows="5" placeholder="Remarks"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-row display-none" id="checkinremarksrow">
                            <div class="form-group col-md-12">
                                <label>Remarks</label>
                                <textarea name="remarks" id="checkinremarks" class="form-control" rows="4" placeholder="Type why this vehicle is unserviceable"></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label for="checkindate" class="control-label"><?php echo trans('lang.checkindate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" readonly required="" placeholder="<?php echo trans('lang.checkindate'); ?>" id="checkindate" name="checkindate" type="text">
                                    <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="checkindate"></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- <input type="hidden" name="employeeid" id="checkinemployeeid" value="0" /> -->
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
    <!-- <div class="modal fade" id="delete" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
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
    </div> -->

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
            $('#checkindate').val(formattedDateTime);
            $('#checkoutdate').val(formattedDateTime);

            function currentTimeValue() {
                var now = new Date();
                var hours = now.getHours();
                var minutes = String(now.getMinutes()).padStart(2, '0');
                var suffix = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                return String(hours).padStart(2, '0') + ':' + minutes + ' ' + suffix;
            }

            function generateVehicleControlNumber() {
                $.ajax({
                    type: "GET",
                    url: "{{ url('generateControlNumber') }}",
                    data: {
                        prefix: 'BF'
                    },
                    dataType: "JSON",
                    success: function(data) {
                        if (data && data.success) {
                            $("#checkoutcontrolno").val(data.message);
                        }
                    }
                });
            }

            var checkoutDivisionItems = [];
            var checkoutPassengerItems = [];

            function renderTripItems(type) {
                var items = type === 'division' ? checkoutDivisionItems : checkoutPassengerItems;
                var listSelector = type === 'division' ? '#checkoutdivisionlist' : '#checkoutpassengerlist';
                var hiddenPrefix = type === 'division' ? '#checkoutdivision' : '#checkoutpassenger';
                var jsonSelector = type === 'division' ? '#checkoutdivisionsjson' : '#checkoutpassengersjson';
                var emptyText = type === 'division' ? 'No division added' : 'No passenger added';

                for (var i = 1; i <= 3; i++) {
                    $(hiddenPrefix + i).val(items[i - 1] || '');
                }
                $(jsonSelector).val(JSON.stringify(items));

                if (!items.length) {
                    $(listSelector).addClass('text-muted').html(emptyText);
                    return;
                }

                var html = items.map(function(item, index) {
                    return '<div class="d-flex justify-content-between align-items-center border-bottom py-1">' +
                        '<span>' + (index + 1) + '. ' + $('<div>').text(item).html() + '</span>' +
                        '<button type="button" class="btn btn-xs btn-link text-danger remove-trip-item" title="Remove" data-type="' + type + '" data-index="' + index + '">&times;</button>' +
                        '</div>';
                }).join('');
                $(listSelector).removeClass('text-muted').html(html);
            }

            function addTripItem(type) {
                var items = type === 'division' ? checkoutDivisionItems : checkoutPassengerItems;
                var inputSelector = type === 'division' ? '#checkoutdivisioninput' : '#checkoutpassengerinput';
                var value = $.trim($(inputSelector).val());

                if (!value) {
                    return;
                }

                if (items.length >= 10) {
                    alert('Maximum of 10 only.');
                    return;
                }

                items.push(value);
                $(inputSelector).val('');
                renderTripItems(type);
            }

            function resetTripItemLists() {
                checkoutDivisionItems = [];
                checkoutPassengerItems = [];
                $('#checkoutdivisioninput, #checkoutpassengerinput').val('');
                renderTripItems('division');
                renderTripItems('passenger');
            }

            $('#addcheckoutdivision').on('click', function() {
                addTripItem('division');
            });

            $('#addcheckoutpassenger').on('click', function() {
                addTripItem('passenger');
            });

            $(document).on('click', '.remove-trip-item', function() {
                var type = $(this).data('type');
                var index = parseInt($(this).data('index'), 10);
                var items = type === 'division' ? checkoutDivisionItems : checkoutPassengerItems;

                if (!isNaN(index)) {
                    items.splice(index, 1);
                    renderTripItems(type);
                }
            });

            $('#checkoutdivisioninput').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    addTripItem('division');
                }
            });

            $('#checkoutpassengerinput').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    addTripItem('passenger');
                }
            });

            function toggleVehicleRemarks(selectId, rowId, textareaId) {
                var statusValue = $('#' + selectId).val();
                var showRemarks = statusValue === 'unserviceable';
                $('#' + rowId).toggle(showRemarks);
                $('#' + textareaId).prop('required', showRemarks);
                if (!showRemarks) {
                    $('#' + textareaId).val('');
                }

                if (selectId === 'checkoutvehiclestatus') {
                    $('#checkouttriprow').toggle(statusValue === 'borrowed');
                }
                if (selectId === 'checkinvehiclestatus') {
                    $('#checkintriprow').toggle(statusValue === 'returned');
                }
            }

            $('#checkoutvehiclestatus').on('change', function() {
                toggleVehicleRemarks('checkoutvehiclestatus', 'checkoutremarksrow', 'checkoutremarks');
            });

            $('#checkinvehiclestatus').on('change', function() {
                toggleVehicleRemarks('checkinvehiclestatus', 'checkinremarksrow', 'checkinremarks');
            });

            $(document).on('input', '.number-only', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            $('.select2').on('select2:select', function(e) {
                var selectedValue = e.params.data.id;

                if (selectedValue === 'brandid') {
                    var url = "{{ URL::to('brandlist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'locationid') {
                    var url = "{{ URL::to('locationlist') }}";
                    window.open(url, '_blank');
                } else if (selectedValue === 'supplierid') {
                    var url = "{{ URL::to('supplierlist') }}";
                    window.open(url, '_blank');
                }
            });


        });


        function assetstatus(status, checkstatus) {
            if (checkstatus == 2) {
                return "<span class='badge badge-data text-white background-yellow'>Borrowed</span>";
            } else if (status == 1) {
                return "<span class='badge badge-data text-white background-green'>Ready to Deploy</span>";
            } else if (status == 2) {
                return "<span class='badge badge-data text-white background-yellow'>Pending</span>";

            } else if (status == 3) {
                return "<span class='badge badge-data text-white background-orange'>Archived</span>";

            } else if (status == 4) {
                return "<span class='badge badge-data text-white background-red'>Broken</span>";

            } else if (status == 5) {
                return "<span class='badge badge-data text-white background-black'>Lost</span>";

            } else if (status == 6) {
                return "<span class='badge badge-data text-white background-red'>Unserviceable</span>";

            } else {
                return "<span class='badge badge-data text-white background-gray'>Undefined</span>";
            }
        }

        function historystatus(checkstatus, historyStatus) {
            console.log(checkstatus)
            if (historyStatus == 3) {
                return "<span class='badge badge-data text-white background-green'>Serviceable</span>";
            } else if (historyStatus == 4) {
                return "<span class='badge badge-data text-white background-red'>Unserviceable</span>";
            } else if (checkstatus == 2 || historyStatus == 1) {
                return "<span class='badge badge-data text-white background-yellow'>Borrowed</span>";
            } else {
                return "<span class='badge badge-data text-white background-blue'>Returned</span>";
            }
        }

        "use strict";
        $('#data').DataTable({
            ajax: "{{ url('assetvehicle')}}",
            columns: [{
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    visible: false
                },
                {
                    data: 'pictures'
                },
                {
                    data: 'assettag'
                },
                {
                    data: 'serial',
                    orderable: false,
                    searchable: false,
                    visible: false
                },
                {
                    data: 'purchasedate',
                    orderable: false,
                    searchable: false,
                    visible: false
                },
                {
                    data: 'cost',
                    orderable: false,
                    searchable: false,
                    visible: false
                },

                {
                    data: 'description',
                    orderable: false,
                    searchable: false,
                    visible: false
                },
                {
                    data: 'name'
                },
                // {
                //     data: 'type'
                // },
                {
                    data: 'brand'
                },
                {
                    data: 'vehiclecategory'
                },
                {
                    data: 'yearmodel'
                },
                {
                    data: 'yearacquired'
                },
                {
                    data: 'chassis'
                },
                {
                    data: 'engineno'
                },
                {
                    data: 'fueltype'
                },
                {
                    data: 'transmission'
                },


                {
                    data: 'location'
                }, {
                    data: function(e) {
                        return assetstatus(e.status, e.checkstatus);
                    },
                },
                {
                    data: function(e) {
                        return historystatus(e.checkstatus, e.historystatus);
                    },
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            buttons: [{
                    extend: 'copy',
                    text: 'Copy <i class="fa fa-files-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.asset_list '); ?>',
                    exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV <i class="fa fa-file-excel-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.asset_list'); ?>',
                    exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF <i class="fa fa-file-pdf-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.asset_list'); ?>',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7, 8, 9, 10]
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader.alignment = 'left';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
                            .join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: '<?php echo trans('lang.asset_list'); ?>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    text: 'Print <i class="fa fa-print"></i>',
                    exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                }
            ]
        });







        //get all asset type
        // $.ajax({
        //     type: "GET",
        //     url: "{{ url('listassettype')}}",
        //     dataType: "JSON",
        //     success: function(html) {
        //         var objs = html.message;
        //         jQuery.each(objs, function(index, record) {
        //             var id = decodeURIComponent(record.id);
        //             var name = decodeURIComponent(record.name);
        //             $("#typeid").append($("<option></option>")
        //                 .attr("value", id)
        //                 .text(name));
        //             $("#edittypeid").append($("<option></option>")
        //                 .attr("value", id)
        //                 .text(name));
        //         });
        //     }
        // });

        // $.ajax({
        //     type: "GET",
        //     url: "{{ url('listassettype')}}",
        //     dataType: "JSON",
        //     success: function(html) {
        //         var objs = html.message;
        //         var excludedTypeIds = ['1']; // Replace with the specific typeids you want to exclude

        //         jQuery.each(objs, function(index, record) {
        //             var id = decodeURIComponent(record.id);
        //             var name = decodeURIComponent(record.name);

        //             if (excludedTypeIds.includes(id)) {
        //                 return; // Skip this iteration if the typeid is in the excluded list
        //             }

        //             $("#typeid").append($("<option></option>")
        //                 .attr("value", id)
        //                 .text(name));
        //             $("#edittypeid").append($("<option></option>")
        //                 .attr("value", id)
        //                 .text(name));
        //         });
        //     }
        // });


        function showreference() {
            $("#supplierid, #editsupplierid, #brandid, #editbrandid, #locationid, #editlocationid").empty();

            //get all supplier
            $.ajax({
                type: "GET",
                url: "{{ url('listsupplier')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#supplierid").append($("<option></option>")
                        .attr("value", "")
                        .attr("selected", "")
                        .attr("disabled", "")
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

            //get all brand 
            $.ajax({
                type: "GET",
                url: "{{ url('listofvehiclebrand')}}",
                dataType: "JSON",
                success: function(html) {
                    $("#brandid").append($("<option></option>")
                        .attr("value", "")
                        .attr("selected", "")
                        .attr("disabled", "")
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
                        .attr("selected", "")
                        .attr("disabled", "")
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

        }



        // removed
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
                var serial = $("#serial").val();
                var quantity = $("#quantity").val();
                var purchasedate = $("#purchasedate").val();
                var cost = $("#cost").val();
                var warranty = $("#warranty").val();
                var status = $("#status").val();
                var description = $("#description").val();
                var vehiclecategory = $("#vehiclecategory").val();
                // var vehicledesc = $("#vehicledesc").val();
                var yearmodel = $("#yearmodel").val();
                var yearacquired = $("#yearacquired").val();
                var chassis = $("#chassis").val();
                var engineno = $("#engineno").val();
                var fueltype = $("#fueltype").val();
                var transmission = $("#transmission").val();
                var picture = $('#picture')[0].files[0];


                form.append('name', name);
                form.append('locationid', locationid);
                form.append('supplierid', supplierid);
                form.append('brandid', brandid);
                form.append('typeid', typeid);
                form.append('assettag', assettag);
                form.append('serial', serial);
                form.append('quantity', quantity);
                form.append('purchasedate', purchasedate);
                form.append('cost', cost);
                form.append('warranty', warranty);
                form.append('status', status);
                form.append('description', description);
                form.append('vehiclecategory', vehiclecategory);
                // form.append('vehicledesc', vehicledesc);
                form.append('yearmodel', yearmodel);
                form.append('yearacquired', yearacquired);
                form.append('chassis', chassis);
                form.append('engineno', engineno);
                form.append('fueltype', fueltype);
                form.append('transmission', transmission);
                form.append('picture', picture);

                $.ajax({
                    type: "POST",
                    url: "{{ url('saveassetvehicle')}}",
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
                var serial = $("#editserial").val();
                var quantity = $("#editquantity").val();
                var purchasedate = $("#editpurchasedate").val();
                var cost = $("#editcost").val();
                var warranty = $("#editwarranty").val();
                var status = $("#editstatus").val();
                var description = $("#editdescription").val();
                var vehiclecategory = $("#editvehiclecategory").val();
                // var vehicledesc = $("#vehicledesc").val();
                var yearmodel = $("#edityearmodel").val();
                var yearacquired = $("#edityearacquired").val();
                var chassis = $("#editchassis").val();
                var engineno = $("#editengineno").val();
                var fueltype = $("#editfueltype").val();
                var transmission = $("#edittransmission").val();
                var picture = $('#editpicture')[0].files[0];


                form.append('id', id);
                form.append('name', name);
                form.append('locationid', locationid);
                form.append('supplierid', supplierid);
                form.append('brandid', brandid);
                form.append('typeid', typeid);
                form.append('assettag', assettag);
                form.append('serial', serial);
                form.append('quantity', quantity);
                form.append('purchasedate', purchasedate);
                form.append('cost', cost);
                form.append('warranty', warranty);
                form.append('status', status);
                form.append('description', description);
                form.append('vehiclecategory', vehiclecategory);
                // form.append('vehicledesc', vehicledesc);
                form.append('yearmodel', yearmodel);
                form.append('yearacquired', yearacquired);
                form.append('chassis', chassis);
                form.append('engineno', engineno);
                form.append('fueltype', fueltype);
                form.append('transmission', transmission);
                form.append('picture', picture);

                $.ajax({
                    type: "POST",
                    url: "{{ url('updateassetvehicle')}}",
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

        //delete data
        $("#formdelete").validate({
            submitHandler: function(form) {
                $.ajax({
                    method: "POST",
                    url: "{{ url('deleteassetvehicle')}}",
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

        //show edit data
        // $('#edit').on('show.bs.modal', function(e) {
        //     var $modal = $(this),
        //         id = $(e.relatedTarget).attr('customdata');
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ url('assetbyidvehicle')}}",
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
        //             // $("#editserial").val(data.message.serial);
        //             // $("#editquantity").val(data.message.quantity);
        //             $("#editpurchasedate").val(data.message.purchasedate);
        //             $("#editcost").val(data.message.cost);
        //             $("#editwarranty").val(data.message.warranty);
        //             $("#editstatus").val(data.message.status);
        //             $("#editdescription").val(data.message.assetdescription);
        //             $("#editvehiclecategory").val(data.message.vehiclecategory);
        //             $("#edityearmodel").val(data.message.yearmodel);
        //             $("#edityearacquired").val(data.message.yearacquired);
        //             $("#editchassis").val(data.message.chassis);
        //             $("#editengineno").val(data.message.engineno);
        //             $("#editfueltype").val(data.message.fueltype);
        //             $("#edittransmission").val(data.message.transmission);
        //             $("#editdescription").val(data.message.assetdescription);
        //         }
        //     });
        // });



        // delete and edit validations


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
                    url: "{{ url('assetbyidvehicle') }}",
                    data: {
                        id: id
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $("#editid").val(id);
                        $("#editname").val(data.message.assetname);
                        $("#editlocationid").val(data.message.locationid);
                        $("#editsupplierid").val(data.message.supplierid);
                        $("#editbrandid").val(data.message.brandid);
                        $("#edittypeid").val(data.message.typeid);
                        $("#editassettag").val(data.message.assettag);
                        // $("#editserial").val(data.message.serial);
                        // $("#editquantity").val(data.message.quantity);
                        $("#editpurchasedate").val(data.message.purchasedate);
                        $("#editcost").val(data.message.cost);
                        $("#editwarranty").val(data.message.warranty);
                        $("#editstatus").val(data.message.status);
                        $("#editdescription").val(data.message.assetdescription);
                        $("#editvehiclecategory").val(data.message.vehiclecategory);
                        $("#edityearmodel").val(data.message.yearmodel);
                        $("#edityearacquired").val(data.message.yearacquired);
                        $("#editchassis").val(data.message.chassis);
                        $("#editengineno").val(data.message.engineno);
                        $("#editfueltype").val(data.message.fueltype);
                        $("#edittransmission").val(data.message.transmission);
                        $("#editdescription").val(data.message.assetdescription);
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
                            url: "{{ url('deleteassetvehicle')}}",
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
                        alert("Password is not valid");

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

        $('#edit').on('hidden.bs.modal', function() {
            $("#editcontent").css('display', 'none');
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
        $("#formcheckout").validate({
            submitHandler: function(form) {
                var shouldOpenPrint = $("#checkoutvehiclestatus").val() === 'borrowed';
                var printWindow = shouldOpenPrint ? window.open('', '_blank') : null;
                if (printWindow) {
                    printWindow.document.write('<p style="font-family: Arial; padding: 20px;">Preparing borrowed form...</p>');
                }

                $.ajax({
                    method: "POST",
                    url: "{{ url('savecheckoutvehicle')}}",
                    data: $("#formcheckout").serialize(),
                    dataType: "JSON",
                    success: function(data) {
                        console.log(data);
                        $("#checkoutsuccess").css({
                            'display': "block"
                        });
                        if (data.print_url) {
                            if (printWindow) {
                                printWindow.location = data.print_url;
                            } else {
                                window.location.href = data.print_url;
                                return;
                            }
                        } else if (printWindow) {
                            printWindow.close();
                        }
                        $('#checkout').modal('hide');
                        window.setTimeout(function() {
                            location.reload()
                        }, 2000)
                    },
                    error: function() {
                        if (printWindow) {
                            printWindow.close();
                        }
                    }
                });
            }
        });


        //checkin
        $("#formcheckin").validate({
            submitHandler: function(form) {
                var shouldOpenPrint = $("#checkinvehiclestatus").val() === 'returned';
                var printWindow = shouldOpenPrint ? window.open('', '_blank') : null;
                if (printWindow) {
                    printWindow.document.write('<p style="font-family: Arial; padding: 20px;">Preparing return form...</p>');
                }

                $.ajax({
                    method: "POST",
                    url: "{{ url('savecheckinvehicle')}}",
                    data: $("#formcheckin").serialize(),
                    dataType: "JSON",
                    success: function(data) {
                        console.log(data);
                        $("#checkinsuccess").css({
                            'display': "block"
                        });
                        if (data.print_url) {
                            if (printWindow) {
                                printWindow.location = data.print_url;
                            } else {
                                window.location.href = data.print_url;
                                return;
                            }
                        } else if (printWindow) {
                            printWindow.close();
                        }
                        $('#checkin').modal('hide');
                        window.setTimeout(function() {
                            location.reload()
                        }, 2000)
                    },
                    error: function() {
                        if (printWindow) {
                            printWindow.close();
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
                url: "{{ url('assetbyidvehicle')}}",
                data: {
                    id: id
                },
                dataType: "JSON",
                success: function(data) {
                    $("#assetid").val(id);
                    $("#checkoutname").val(data.message.assetname);
                    $("#checkoutassettag").val(data.message.assettag);
                    $("#checkoutvehiclestatus").val('borrowed');
                    $("#checkoutremarks").val('');
                    $("#checkoutremarksrow").hide();
                    $("#checkoutremarks").prop('required', false);
                    $("#checkouttriprow").show();
                    $("#checkoutcontrolno, #checkoutdriver, #checkoutphone, #checkoutdestination, #checkoutpurpose, #checkoutdeparture, #checkoutmileage, #checkoutdivision1, #checkoutdivision2, #checkoutdivision3, #checkoutdivisionsjson, #checkoutpassenger1, #checkoutpassenger2, #checkoutpassenger3, #checkoutpassengersjson, #checkoutsignature, #checkoutsupervising, #checkouttripremarks").val('');
                    resetTripItemLists();
                    $("#checkoutdateborrowed").val(($("#checkoutdate").val() || '').split(' ')[0]);
                    $("#checkoutdeparture").val(currentTimeValue());
                    generateVehicleControlNumber();
                }
            });
        });





        //show checkin
        $('#checkin').on('show.bs.modal', function(e) {
            var $modal = $(this),
                id = $(e.relatedTarget).attr('customdata');
            $.ajax({
                type: "POST",
                url: "{{ url('assetbyidvehicle')}}",
                data: {
                    id: id
                },
                dataType: "JSON",
                success: function(data) {
                    $("#checkinassetid").val(id);
                    $("#checkinname").val(data.message.name);
                    $("#checkinassettag").val(data.message.assettag);
                    $("#checkinvehiclestatus").val('returned');
                    $("#checkinremarks").val('');
                    $("#checkinremarksrow").hide();
                    $("#checkinremarks").prop('required', false);
                    $("#checkintriprow").show();
                    $("#checkindatereturn").val(($("#checkindate").val() || '').split(' ')[0]);
                    $("#checkinarrival").val(currentTimeValue());
                    $("#checkinmileage, #checkinreturnremarks, #checkincontrolno, #checkinreturningname").val('');
                }
            });
        });

        $("#add").on('show.bs.modal', function(e) {
            showreference();
        });

        $("#checkout").on('show.bs.modal', function(e) {
            showreference();
        });

        $("#checkin").on('show.bs.modal', function(e) {
            showreference();
        });

        //show delete data

        $('#delete').on('show.bs.modal', function(e) {
            var $modal = $(this),
                id = $(e.relatedTarget).attr('customdata');
            $("#iddelete").val(id);
        });

        // status change




    })(jQuery);
</script>
@endsection
