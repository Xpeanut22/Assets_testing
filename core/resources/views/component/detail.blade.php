@extends('main')
@section('content')

<section class="">
    <div class="content p-4">
        <div class="row pt-3">
            <div class="col-md-8">
                <h3 class=""><?php echo trans('lang.issue'); ?></h3>
            </div>
            <div class="col-md-4 text-md-right">
                <a target="_blank" href="{{url('componentlist/generatelabel', $componentid)}}" id="btndetail" class="btn btn-sm btn-fill btn-primary"><i
                        class="ti-info"></i> <?php echo trans('lang.generatelabel'); ?></a>
                <a href="{{ url('componentlist') }}" id="btndetail" class="btn btn-sm btn-fill btn-warning"><i
                        class="ti-info"></i> <?php echo trans('lang.backtocomponent'); ?></a>

            </div>

        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-9">
                                <input type="hidden" value="{{ $componentid }}" name="componentid" id="componentid" />
                                <p class="title-detail font-bold"> <span class="componentname"></span> (<span class="serial"></span>) </p>
                                <p class="assetdetail"><span class="type"></span> &bull; <span
                                class="assetstatus"></span></p>
                            </div>
                            <div class="col-md-3  ">
                                <div class="border p-2 barcode-inner">
                                    <div class="assetbarcode"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div id="messagesuccess" class="display-none alert alert-success"><?php echo trans('lang.data_added'); ?></div>
                                <div id="messagedelete" class="display-none alert alert-success"><?php echo trans('lang.data_deleted'); ?></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="checkinsuccess" class="alert alert-success display-none"><?php echo trans('lang.data_checkin_succeess'); ?></div>
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details"
                                            role="tab" aria-controls="details"
                                            aria-selected="true"><?php echo trans('lang.details'); ?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="file-tab" data-toggle="tab" href="#file"
                                            role="tab" aria-controls="file"
                                            aria-selected="false"><?php echo trans('lang.file'); ?></a>
                                    </li>
                                    <!-- <li class="nav-item">
                                        <a class="nav-link" id="depreciate-tab" data-toggle="tab" href="#depreciate"
                                            role="tab" aria-controls="depreciate"
                                            aria-selected="false"><?php echo trans('lang.depreciation'); ?></a>
                                    </li> -->
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="details" role="tabpanel"
                                        aria-labelledby="details-tab">
                                        <div class="table-responsive  pt-4">
                                            <table id="datacomponent" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th><?php echo trans('lang.controlno'); ?></th>
                                                        <!-- <th><?php echo trans('lang.name'); ?></th> -->
                                                        <th><?php echo trans('lang.quantity'); ?></th>
                                                        <th><?php echo trans('lang.date'); ?></th>
                                                        <th><?php echo trans('lang.remarks'); ?></th>
                                                        <th><?php echo trans('lang.issueto'); ?></th>
                                                        <th><?php echo trans('lang.mobileno'); ?></th>
                                                        <th><?php echo trans('lang.department'); ?></th>
                                                        <th><?php echo trans('lang.createby'); ?></th>
                                                        <th><?php echo trans('lang.action'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th><?php echo trans('lang.controlno'); ?></th>
                                                        <!-- <th><?php echo trans('lang.name'); ?></th> -->
                                                        <th><?php echo trans('lang.quantity'); ?></th>
                                                        <th><?php echo trans('lang.date'); ?></th>
                                                        <th><?php echo trans('lang.remarks'); ?></th>
                                                        <th><?php echo trans('lang.issueto'); ?></th>
                                                        <th><?php echo trans('lang.mobileno'); ?></th>
                                                        <th><?php echo trans('lang.department'); ?></th>
                                                        <th><?php echo trans('lang.createby'); ?></th>
                                                        <th><?php echo trans('lang.action'); ?></th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="file" role="tabpanel"
                                        aria-labelledby="file-tab">
                                        <div class="text-md-right text-left pt-2">
                                            <button type="button" data-toggle="modal" data-target="#addfile" class="btn btn-sm btn-fill btn-primary"><i class="fa fa-plus"></i> <?php echo trans('lang.add_data'); ?></button>
                                        </div>
                                        <div class="table-responsive  pt-4">
                                            <table id="datafile" class="table table-striped table-bordered"
                                                cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th><?php echo trans('lang.date'); ?></th>
                                                        <th><?php echo trans('lang.name'); ?></th>
                                                        <th><?php echo trans('lang.file'); ?></th>
                                                        <th><?php echo trans('lang.action'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th><?php echo trans('lang.date'); ?></th>
                                                        <th><?php echo trans('lang.name'); ?></th>
                                                        <th><?php echo trans('lang.file'); ?></th>
                                                        <th><?php echo trans('lang.action'); ?></th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="depreciate" role="tabpanel"
                                        aria-labelledby="depreciate-tab">

                                        <div class="table-responsive  pt-4">
                                            <table id="datadepreciate" class="table table-striped table-bordered"
                                                cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo trans('lang.period'); ?></th>
                                                        <th><?php echo trans('lang.bookvalue'); ?></th>
                                                        <th><?php echo trans('lang.depreciationpercentage'); ?></th>
                                                        <th><?php echo trans('lang.amount'); ?></th>
                                                        <th><?php echo trans('lang.accumulateddepreciation'); ?></th>
                                                        <th><?php echo trans('lang.endingbookvalue'); ?></th>
                                                    </tr>
                                                </thead>

                                                <tfoot>
                                                    <tr>
                                                        <th><?php echo trans('lang.period'); ?></th>
                                                        <th><?php echo trans('lang.bookvalue'); ?></th>
                                                        <th><?php echo trans('lang.depreciationpercentage'); ?></th>
                                                        <th><?php echo trans('lang.amount'); ?></th>
                                                        <th><?php echo trans('lang.accumulateddepreciation'); ?></th>
                                                        <th><?php echo trans('lang.endingbookvalue'); ?></th>
                                                    </tr>
                                                </tfoot>
                                                <tbody id="calculator-list">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
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
                            <div id="checkinfailed" class="display-none alert alert-success"><?php echo trans('lang.data_checkin_failed_quantity'); ?></div>
                            <div class="form-row">

                                <div class="form-group col-md-12">
                                    <label><?php echo trans('lang.component'); ?></label>
                                    <input name="assetname" type="text" readonly id="checkinname" class="componentname form-control" required placeholder="<?php echo trans('lang.component'); ?>" />
                                </div>

                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label><?php echo trans('lang.quantity'); ?></label>
                                    <input name="quantity" type="text" id="checkinquantity" class=" form-control" required placeholder="<?php echo trans('lang.quantity'); ?>" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12 mb-0">
                                    <label for="checkindate" class="control-label"><?php echo trans('lang.checkindate'); ?></label>
                                    <div class="input-group mb-0">
                                        <input class="form-control setdate" required="" placeholder="<?php echo trans('lang.checkindate'); ?>" id="checkindate" name="checkindate" type="text">
                                        <span class="input-group-addon border-1" id="date"><i class="fa fa-calendar"></i></span>
                                    </div>
                                    <label class="error" for="checkoutdate"></label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="historyid" id="historyid" />
                            <input type="hidden" name="componentid" id="checkincomponentid" />
                            <input type="hidden" name="assetid" id="checkinassetid" />

                            <button type="submit" class="btn btn-primary"
                                id="savecheckin"><?php echo trans('lang.save'); ?></button>
                            <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end checkin-->

    </div>

    <!--add new data -->
    <div id="addfile" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" id="formadd">
                    <div class="modal-header">

                        <h5 class="modal-title"><?php echo trans('lang.add_data'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label><?php echo trans('lang.name'); ?></label>
                            <input name="name" type="text" id="name" class=" form-control" required placeholder="<?php echo trans('lang.name'); ?>" />
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.file'); ?></label>
                            <input name="filename" type="file" id="filename" class=" form-control" required accept="image/*,application/pdf, .xls,.xlsx,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                            <small class="d-block">File allowed are jpeg, png, jpg, pdf, doc, docx, xls, xlsx (max 2mb)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"
                            id="save"><?php echo trans('lang.save'); ?></button>
                        <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                        <span class="saving_data d-none"><?php echo trans('lang.saving_data'); ?></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end add data-->

    <!--delete data -->
    <div class="modal fade" id="deletefile" role="dialog">
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
    </div>
    <!--end delete data -->
</section>

<script>
    (function($) {
        "use strict";

        var componentid = $("#componentid").val();
        $.ajax({
            type: "POST",
            url: "{{ url('componentbyid')}}",
            data: {
                id: componentid
            },
            dataType: "JSON",
            success: function(data) {
                $(".componentname").html(data.message.componentname);
                $(".serial").html(data.message.serial);
                $(".serial").html(data.message.serial);
                $(".type").html(data.message.type);
                $(".assetstatus").html(data.assetstatus);

                $(".assetbarcode").html(data.assetbarcode);
            }
        });



        //component data
        $('#datacomponent').DataTable({

            ajax: {
                url: "{{ url('historycomponentbyid')}}",
                type: "post",
                data: function(d) {
                    d.id = componentid;
                }
            },

            columns: [{
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    visible: false
                },
                {
                    data: 'control_number'
                },
                // {
                //     data: 'name'
                // },
                {
                    data: 'quantity'
                },
                {
                    data: 'date'
                },
                {
                    data: 'remarks'
                },
                {
                    data: 'efullname'
                },
                {
                    data: 'mobile_number'
                },
                {
                    data: 'name'
                },

                {
                    data: 'rfullname'
                },
                {
                    data: 'action'
                },


            ],

            buttons: [{
                    extend: 'copy',
                    text: 'Copy <i class="fa fa-files-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.componentdetail '); ?>',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV <i class="fa fa-file-excel-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.componentdetail'); ?>',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8]

                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF <i class="fa fa-file-pdf-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.componentdetail'); ?>',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8]

                    },
                    customize: function(doc) {
                        doc.styles.tableHeader.alignment = 'left';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
                            .join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: '<?php echo trans('lang.componentdetail'); ?>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    text: 'Print <i class="fa fa-print"></i>',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8]

                    }
                }
            ]
        });

        //checkin
        $("#formcheckin").validate({
            rules: {
                quantity: {
                    required: true,
                    digits: true
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    method: "POST",
                    url: "{{ url('savecheckincomponent')}}",
                    data: $("#formcheckin").serialize(),
                    dataType: "JSON",
                    success: function(data) {
                        if (data.success == '0') {
                            $("#checkinfailed").css({
                                'display': "block"
                            });
                        }
                        if (data.success == 'success') {
                            $("#checkinsuccess").css({
                                'display': "block"
                            });
                            $('#checkin').modal('hide');
                            window.setTimeout(function() {
                                location.reload()
                            }, 2000)
                        }
                    }
                });
            }
        });

        //getdetail
        $('#checkin').on('show.bs.modal', function(e) {
            var $modal = $(this),
                id = $(e.relatedTarget).attr('customdata');
            $.ajax({
                type: "POST",
                url: "{{ url('singlehistorycomponentbyid')}}",
                data: {
                    id: id
                },
                dataType: "JSON",
                success: function(data) {
                    $("#historyid").val(id);
                    $("#checkincomponentid").val(data.message.componentid);
                    $("#checkinassetid").val(data.message.assetid);
                }
            });
        });

        //get calculator
        $.ajax({
            type: "POST",
            url: "{{ url('componentdepreciationvaluebyid')}}",
            data: {
                id: componentid
            },
            dataType: "JSON",
            success: function(html) {
                if (html.success == 'failed') {
                    $("#calculator-list").append($("<tr><td align='center' colspan='6'>No Data</td></tr>"));
                } else {
                    var objs = html.message;
                    var id = decodeURIComponent(objs.componentid);
                    var cost = decodeURIComponent(objs.componentcost);
                    var assetvalue = decodeURIComponent(objs.assetvalue);
                    var period = decodeURIComponent(objs.period);
                    var per = 1;
                    var endvalue = 0;
                    var percentage;
                    var accumulateddep = 0;
                    var annualdep = (cost - assetvalue) / period;
                    accumulateddep = annualdep;
                    endvalue = cost - annualdep;
                    percentage = 100 / period;

                    for (per = 1; per <= period; per++) {
                        $("#calculator-list").append($("<tr><td>" + per + "</td><td>" + cost + "</td><td>" + percentage.toFixed(2) + "%</td><td>" + annualdep.toFixed(2) + "</td><td>" + accumulateddep.toFixed(2) + "</td> <td>" + endvalue.toFixed(2) + "</td></tr>"));
                        cost = endvalue.toFixed(2);
                        accumulateddep += annualdep;
                        endvalue = cost - annualdep;
                    };
                }
            }
        });

        //File data
        $('#datafile').DataTable({
            ajax: {
                url: "{{ url('filecomponentbyid')}}",
                type: "post",
                data: function(d) {
                    d.componentid = componentid;
                },
            },
            columns: [{
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    visible: false
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'name'
                },
                {
                    data: 'filename'
                },
                {
                    data: 'action'
                }
            ],
            buttons: [{
                    extend: 'copy',
                    text: 'Copy <i class="fa fa-files-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.file_list '); ?>',
                    exportOptions: {
                        columns: [1, 2, 3]
                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV <i class="fa fa-file-excel-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.file_list '); ?>',
                    exportOptions: {
                        columns: [1, 2, 3]
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF <i class="fa fa-file-pdf-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.file_list '); ?>',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [1, 2, 3]
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader.alignment = 'left';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
                            .join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: '<?php echo trans('lang.file_list '); ?>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    text: 'Print <i class="fa fa-print"></i>',
                    exportOptions: {
                        columns: [1, 2, 3]
                    }
                }
            ]
        });

        //add data
        $("#formadd").validate({
            submitHandler: function(form) {
                var form = new FormData();
                var name = $("#name").val();
                var componentids = componentid;
                var filename = $('#filename')[0].files[0];

                form.append('name', name);
                form.append('componentid', componentids);
                form.append('filename', filename);
                alert(componentids);
                $.ajax({
                    type: "POST",
                    url: "{{ url('savefile')}}",
                    data: form,
                    contentType: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#save').html($(".saving_data").html());
                        $('#save').prop("disabled", true);
                    },
                    success: function(data) {
                        if (data.message == 'success') {
                            $("#messagesuccess").css({
                                'display': "block"
                            });
                            $('#add').modal('hide');
                            $('#save').prop("disabled", false);
                            window.setTimeout(function() {
                                location.reload()
                            }, 2000);
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
                    url: "{{ url('deletefile')}}",
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


        //show delete data
        $('#deletefile').on('show.bs.modal', function(e) {
            var $modal = $(this),
                id = $(e.relatedTarget).attr('customdata');
            $("#iddelete").val(id);
        });
    })(jQuery);
</script>
@endsection