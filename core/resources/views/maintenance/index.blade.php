@extends('main')
@section('content')

<section class="">
    <div class="content p-4">
        <div class="row pt-3">
            <div class="col-md-6">
                <h3 class=""><?php echo trans('lang.maintenance_list'); ?></h3>
            </div>
            <div class="col-md-6 text-md-right pb-md-0 pb-3">
                <button type="button" data-toggle="modal" data-target="#add" class="btn btn-sm btn-fill btn-primary"><i class="fa fa-plus"></i> <?php echo trans('lang.add_data'); ?></button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body ">
                        <div id="messagesuccess" class="display-none alert alert-success"><?php echo trans('lang.data_added'); ?></div>
                        <div id="messagedelete" class="display-none alert alert-success"><?php echo trans('lang.data_deleted'); ?></div>
                        <div id="messageupdate" class="display-none alert alert-success"><?php echo trans('lang.data_updated'); ?></div>
                        <div class="table-responsive">
                            <table id="data" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.assettag'); ?></th>
                                        <th><?php echo trans('lang.name'); ?></th>
                                        <th><?php echo trans('lang.maintenancetype'); ?></th>
                                        <th><?php echo trans('lang.startdate'); ?></th>
                                        <th><?php echo trans('lang.enddate'); ?></th>
                                        <th><?php echo trans('lang.remarks'); ?></th>
                                        <th><?php echo trans('lang.maintainby'); ?></th>
                                        <th><?php echo trans('lang.action'); ?></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.assettag'); ?></th>
                                        <th><?php echo trans('lang.name'); ?></th>
                                        <th><?php echo trans('lang.maintenancetype'); ?></th>
                                        <th><?php echo trans('lang.startdate'); ?></th>
                                        <th><?php echo trans('lang.enddate'); ?></th>
                                        <th><?php echo trans('lang.remarks'); ?></th>
                                        <th><?php echo trans('lang.maintainby'); ?></th>
                                        <th><?php echo trans('lang.action'); ?></th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--add new data -->
    <div id="add" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" id="formadd" autocomplete="off">
                    <div class="modal-header">

                        <h5 class="modal-title"><?php echo trans('lang.add_data'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label><?php echo trans('lang.asset'); ?></label>
                            <select name="assetid" id="assetid" required class="form-control">
                                <option value=""><?php echo trans('lang.asset'); ?></option>
                            </select>
                        </div>
                        <!-- <div class="form-group" >
                            <label><?php echo trans('lang.supplier'); ?></label>
                                <select name="supplierid" id="supplierid" required class="form-control">
                                    <option value=""><?php echo trans('lang.supplier'); ?></option>
                                </select>
                        </div> -->
                        <div class="form-group">
                            <label><?php echo trans('lang.maintenancetype'); ?></label>
                            <select name="type" id="type" required class="form-control">
                                <option value=""><?php echo trans('lang.type'); ?></option>
                                <option value="<?php echo trans('lang.Maintenance'); ?>"><?php echo trans('lang.Maintenance'); ?></option>
                                <option value="<?php echo trans('lang.Repair'); ?>"><?php echo trans('lang.Repair'); ?></option>
                                <option value="<?php echo trans('lang.Upgrade'); ?>"><?php echo trans('lang.Upgrade'); ?></option>
                                <option value="<?php echo trans('lang.Testing'); ?>"><?php echo trans('lang.Testing'); ?></option>
                                <option value="<?php echo trans('lang.Calibration'); ?>"><?php echo trans('lang.Calibration'); ?></option>
                                <option value="<?php echo trans('lang.Softwaresupport'); ?>"><?php echo trans('lang.Softwaresupport'); ?></option>
                                <option value="<?php echo trans('lang.Hardwaresupport'); ?>"><?php echo trans('lang.Hardwaresupport'); ?></option>
                                <option value="<?php echo trans('lang.others'); ?>"><?php echo trans('lang.others'); ?></option>
                            </select>
                        </div>
                        <!-- <div class="form-group" >
                            <label>Total Amount</label>
                            <input name="mamount" type="number" id="mamount" class=" form-control" required min="0.00" placeholder="0.00"/>
                        </div> -->

                        <div class="form-group">
                            <label>Reason/Remarks</label>
                            <textarea name="reason_remarks" id="reason_remarks" class=" form-control" required rows="5" placeholder="Enter reason and other details here"></textarea>
                        </div>

                        <!-- <div class="form-group mb-0">
                            <label for="startdate" class="control-label"><?php echo trans('lang.startdate'); ?></label>
                            <div class="input-group mb-0">
                                <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.startdate'); ?>" id="startdate" name="startdate" type="text">
                                <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                            </div>
                            <label class="error" for="startdate"></label>
                        </div> -->
                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label>Start Date/Time</label>
                                <div class="input-group mb-0">
                                    <input class="form-control" readonly required placeholder="Please select date"
                                        id="startdate" name="startdate" type="datetime-local">
                                    <span class="input-group-addon border-1" id="date"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label>End Date/Time</label>
                                <div class="input-group mb-0">
                                    <input class="form-control"  placeholder="Please select date"
                                        id="enddate" name="enddate" type="datetime-local">
                                    <span class="input-group-addon border-1" id="date"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="form-group  mb-0">
                            <label for="enddate" class="control-label">End Date</label>
                            <div class="input-group mb-0">
                                <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.enddate'); ?>" id="enddate" name="enddate" type="text">
                                <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                            </div>
                            <label class="error" for="enddate"></label>
                        </div> -->
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.maintainby'); ?></label>
                                <select name="receivedby" id="receivedby" required class="form-control">
                                    <option value="">Select</option>
                                </select>
                            </div>
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
        <div class="modal-dialog">
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
                <form action="#" id="formedit">
                    <div class="modal-header">

                        <h5 class="modal-title"><?php echo trans('lang.edit_data'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label><?php echo trans('lang.asset'); ?></label>
                            <select name="assetid" id="editassetid" required class="form-control">
                                <option value=""><?php echo trans('lang.asset'); ?></option>
                            </select>
                        </div>
                        <!-- <div class="form-group">
                            <label><?php echo trans('lang.supplier'); ?></label>
                            <select name="supplierid" id="editsupplierid" required class="form-control">
                                <option value=""><?php echo trans('lang.supplier'); ?></option>
                            </select>
                        </div> -->
                        <div class="form-group">
                            <label><?php echo trans('lang.type'); ?></label>
                            <select name="type" id="edittype" required class="form-control">
                                <option value=""><?php echo trans('lang.type'); ?></option>
                                <!-- <option value="<?php echo trans('lang.Maintenance'); ?>"><?php echo trans('lang.Maintenance'); ?></option> -->
                                <option value="<?php echo trans('lang.Repair'); ?>"><?php echo trans('lang.Repair'); ?></option>
                                <option value="<?php echo trans('lang.Upgrade'); ?>"><?php echo trans('lang.Upgrade'); ?></option>
                                <option value="<?php echo trans('lang.Testing'); ?>"><?php echo trans('lang.Testing'); ?></option>
                                <option value="<?php echo trans('lang.Calibration'); ?>"><?php echo trans('lang.Calibration'); ?></option>
                                <option value="<?php echo trans('lang.Softwaresupport'); ?>"><?php echo trans('lang.Softwaresupport'); ?></option>
                                <option value="<?php echo trans('lang.Hardwaresupport'); ?>"><?php echo trans('lang.Hardwaresupport'); ?></option>
                                <option value="Operational">Operational</option>

                            </select>
                        </div>

                        <div class="form-group">
                            <label>Total Amount</label>
                            <input name="mamount" type="text" id="editmamount" class=" form-control" required placeholder="0.00" />
                        </div>
                        <div class="form-group">
                            <label>Reason/Remarks</label>
                            <textarea name="reason_remarks" id="editreason_remarks" class=" form-control" required rows="5" placeholder="Enter reason and other details here"></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label>Start Date</label>
                                <div class="input-group mb-0">
                                    <input class="form-control" readonly required placeholder="Please select date"
                                        id="editstartdate" name="editstartdate" type="datetime-local">
                                    <span class="input-group-addon border-1" id="editdate"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label>End Date</label>
                                <div class="input-group mb-0">
                                    <input class="form-control"  required placeholder="Please select date"
                                        id="editstartdate" name="editenddate" type="datetime-local">
                                    <span class="input-group-addon border-1" id="editdate"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                            </div>
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
            $('#startdate').val(formattedDateTime);
            // $('#editstartdate').val(formattedDateTime);

            // $('#enddate').val(formattedDateTime);


        });



        "use strict";
        //get all supplier
        $.ajax({
            type: "GET",
            url: "{{ url('listsupplier')}}",
            dataType: "JSON",
            success: function(html) {
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
            }
        });


        //get all asset
        $.ajax({
            type: "GET",
            url: "{{ url('listasset')}}",
            dataType: "JSON",
            success: function(html) {
                var objs = html.message;
                jQuery.each(objs, function(index, record) {
                    var id = decodeURIComponent(record.id);
                    var name = decodeURIComponent(record.name);
                    var tag = decodeURIComponent(record.assettag);

                    $("#assetid").append($("<option></option>")
                        .attr("value", id)
                        .text(name + "(" + tag + ")"));
                    $("#editassetid").append($("<option></option>")
                        .attr("value", id)
                        .text(name + "(" + tag + ")"));
                });
            }
        });

        // get all receiver
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
                });
            }
        });


        $('#data').DataTable({

            ajax: "{{ url('maintenance')}}",

            columns: [{
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    visible: false
                },
                {
                    data: 'assettag'
                },
                {
                    data: 'asset'
                },
                // {
                //     data: 'supplier'
                // },
                {
                    data: 'type'
                },
                // {
                //     data: 'mamount',
                //     render: $.fn.dataTable.render.number( ',', '.', 2),
                //     className: 'text-right'
                // },
                {
                    data: 'startdate'
                },
                {
                    data: 'enddate'
                },
                {
                    data: 'reason_remarks'
                },
                {
                    data: 'fullname'
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
                    title: '<?php echo trans('lang.maintenance_list '); ?>',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV <i class="fa fa-file-excel-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.maintenance_list'); ?>',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF <i class="fa fa-file-pdf-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.maintenance_list'); ?>',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6]
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader.alignment = 'left';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
                            .join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: '<?php echo trans('lang.maintenance_list'); ?>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    text: 'Print <i class="fa fa-print"></i>',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6]
                    }
                }
            ]
        });

        //add data
        $("#formadd").validate({
            submitHandler: function(form) {
                $.ajax({
                    method: "POST",
                    url: "{{ url('savemaintenance')}}",
                    data: $("#formadd").serialize(),
                    dataType: "JSON",
                    success: function(data) {
                        console.log(data);
                        $("#messagesuccess").css({
                            'display': "block"
                        });
                        $('#add').modal('hide');
                        window.setTimeout(function() {
                            location.reload()
                        }, 2000)
                    }
                });
            }
        });

        //edit data
        $("#formedit").validate({
            submitHandler: function(form) {
                $.ajax({
                    method: "POST",
                    url: "{{ url('updatemaintenance')}}",
                    data: $("#formedit").serialize(),
                    dataType: "JSON",
                    success: function(data) {
                        console.log(data);
                        $("#messageupdate").css({
                            'display': "block"
                        });
                        $('#edit').modal('hide');
                        window.setTimeout(function() {
                            location.reload()
                        }, 2000)
                    }
                });
            }
        });

        //delete data
        $("#formdelete").validate({
            submitHandler: function(form) {
                $.ajax({
                    method: "POST",
                    url: "{{ url('deletemaintenance')}}",
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
        //     id = $(e.relatedTarget).attr('customdata');
        // 	$.ajax({
        // 		type: "POST",
        // 		url: "{{ url('maintenancebyid')}}",
        // 		data: {id:id},
        // 		dataType: "JSON",
        // 		success: function(data) {
        // 			$("#editid").val(id);
        //             $("#editassetid").val(data.message.assetid);
        //             $("#editsupplierid").val(data.message.supplierid);
        //             $("#editmamount").val(data.message.mamount);
        //             $("#editreason_remarks").val(data.message.reason_remarks);            
        // 			$("#edittype").val(data.message.type);
        //             $("#editstartdate").val(data.message.startdate);
        //             $("#editenddate").val(data.message.enddate);
        // 		}   
        // 	});
        // });



        var targetModalEvent = null;
        var eventHolder;
        var x;

        function showEditModal() {
            // $("#edit").prop('class', 'modal fade');
            if (targetModalEvent) {
                var $modal = $('#edit'),
                    id = $(targetModalEvent.relatedTarget).attr('customdata');
                $.ajax({
                    type: "POST",
                    url: "{{ url('maintenancebyid') }}",
                    data: {
                        id: id
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $("#editid").val(id);
                        $("#editassetid").val(data.message.assetid);
                        $("#editsupplierid").val(data.message.supplierid);
                        $("#editmamount").val(data.message.mamount);
                        $("#editreason_remarks").val(data.message.reason_remarks);
                        $("#edittype").val(data.message.type);
                        $("#editstartdate").val(data.message.startdate);
                        $("#editenddate").val(data.message.enddate);
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
                            url: "{{ url('deletemaintenance')}}",
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


        //show delete data
        // $('#delete').on('show.bs.modal', function(e) {
        //     var $modal = $(this),
        //         id = $(e.relatedTarget).attr('customdata');
        //     $("#iddelete").val(id);
        // });
    })(jQuery);
</script>
@endsection