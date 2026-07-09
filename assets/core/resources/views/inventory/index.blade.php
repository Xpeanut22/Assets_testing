@extends('main')
@section('content')

<section class="">
    <div class="content p-4">
        <div class="row pt-3">
            <div class="col-md-6">
                <h3 class="">Inventory Master List</h3>
            </div>
            <div class="col-md-6 text-md-right pb-md-0 pb-3">
                <button type="button" data-toggle="modal" data-target="#printmodal"
                    class="btn btn-sm btn-fill btn-primary"><i class="fa fa-plus"></i> Inventory Sheet</button>
                <button type="button" data-toggle="modal" data-target="#add" class="btn btn-sm btn-fill btn-primary"><i
                        class="fa fa-plus"></i> <?php echo trans('lang.add_data'); ?></button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body ">
                        <div id="checkoutsuccess" class="display-none alert alert-success">
                            <?php echo trans('lang.data_checkout_succeess'); ?>
                        </div>
                        <div id="checkinsuccess" class="display-none alert alert-success">
                            <?php echo trans('lang.data_checkin_succeess'); ?>
                        </div>
                        <div id="messagesuccess" class="display-none alert alert-success">
                            <?php echo trans('lang.data_added'); ?>
                        </div>
                        <div id="messagedelete" class="display-none alert alert-success">
                            <?php echo trans('lang.data_deleted'); ?>
                        </div>
                        <div id="messageupdate" class="display-none alert alert-success">
                            <?php echo trans('lang.data_updated'); ?>
                        </div>
                        <div id="messagescansuccess" class="display-none alert alert-success">
                            <?php echo trans('lang.data_scan_succeess'); ?>
                        </div>
                        <div id="messagescaninvalid" class="display-none alert alert-danger">
                            <?php echo trans('lang.data_scan_invalid'); ?>
                        </div>


                        <div class="table-responsive">
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

                                        <th><?php echo trans('lang.status'); ?></th>
                                        <th><?php echo trans('lang.hstatus'); ?></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
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
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="display-none messageexist alert alert-success">
                            <?php echo trans('lang.tag_exist'); ?>
                        </div>
                        <div class="form-row">

                            <div class="form-group col-md-12">
                                <label>Invetory Shift</label>
                                <select name="inventoryshift" id="inventoryshift" required class="form-control">
                                    <option value="" selected>Select Shift</option>
                                    <option value="first">6am to 2pm</option>
                                    <option value="second">2pm to 10pm</option>
                                    <option value="third">10pm to 6am</option>
                                </select>
                            </div>

                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.receivedby'); ?></label>
                                <select name="receivedby" id="receivedby" required class="form-control">
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <h1>Barcode Scanner</h1>
                                <input required class="form-control" type="text" name="scannerinput" id="scannerinput"
                                    placeholder="Scan barcode here" autofocus>
                                <ul class="mt-2" id="item-list"></ul>
                            </div>

                        </div>





                        <button type="button" class="btn btn-primary"
                            id="submitButton"><?php echo trans('lang.save'); ?></button>

                    </div>
                    <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="save"><?php echo trans('lang.save'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                    </div> -->
                </form>
            </div>
        </div>
    </div>
    <!--end add data-->

    <!--add checkout -->
    <div id="printmodal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formcheckout" method="POST" action="{{ url('/exportexcel') }}">
                    @csrf <!-- Laravel's CSRF protection -->
                    <div class="modal-header">
                        <h5 class="modal-title">Inventory Sheet</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>From</label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required placeholder="Please select date"
                                        id="datefrom" name="datefrom" type="datetime-local">
                                    <span class="input-group-addon border-1" id="date"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>To</label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required placeholder="Please select date"
                                        id="dateto" name="dateto" type="datetime-local">
                                    <span class="input-group-addon border-1" id="date1"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="printInventory">Print</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!--end checkout-->


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
                                <input name="assettag" type="text" readonly id="checkinassettag" class=" form-control"
                                    required placeholder="<?php echo trans('lang.assettag'); ?>" />
                            </div>
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.asset'); ?></label>
                                <input name="asset" type="text" readonly id="checkinname" class=" form-control" required
                                    placeholder="<?php echo trans('lang.asset'); ?>" />
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><?php echo trans('lang.checkinto'); ?></label>
                                <select name="employeeid1" id="checkoutemployeeid1" required class="form-control">
                                    <option value=""><?php echo trans('lang.employee'); ?></option>
                                </select>
                            </div>
                        </div>


                        <div class="form-row">
                            <div class="form-group col-md-12 mb-0">
                                <label for="checkindate"
                                    class="control-label"><?php echo trans('lang.checkindate'); ?></label>
                                <div class="input-group mb-0">
                                    <input class="form-control setdate" required=""
                                        placeholder="<?php echo trans('lang.checkindate'); ?>" id="checkindate"
                                        name="checkindate" type="text">
                                    <span class="input-group-addon border-1" id="date"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                                <label class="error" for="checkindate"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo trans('lang.remarks'); ?></label>
                            <textarea class="form-control" name="remarks" id="remarks"
                                placeholder="<?php echo trans('lang.remarkshere'); ?>"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- <input type="hidden" name="employeeid" id="checkinemployeeid1" value="0" /> -->
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


    <!--delete data -->
    <div class="modal fade" id="delete" role="dialog">
        <div class="modal-dialog modal-sm">
            <div id="passwordcontent1" class="passwordcontent modal-content">
                <div class="modal-header">

                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="display-none messageexist alert alert-success"><?php echo trans('lang.tag_exist'); ?>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label style="font-size:13px;font-weight:600;">Please Enter Administrative Password!</label>
                            <input name="adminpassword" type="password" id="adminpassword1" class=" form-control"
                                required placeholder="" />
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="passwordsubmit1">submit</button>
                    <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
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
                        <button type="submit" class="btn btn-primary"
                            id="delete"><?php echo trans('lang.delete'); ?></button>
                        <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo trans('lang.close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end delete data -->
</section>
<script>
    document.addEventListener("DOMContentLoaded", function () {


        let itemList = [];
        let typeItem = [];

        const scannerInput = document.getElementById('scannerinput');
        const itemListElement = document.getElementById('item-list');
        // const saveItemsButton = document.getElementById('save-items');

        scannerInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                const barcode = scannerInput.value.trim();
                if (barcode) {
                    addItem(barcode);
                    scannerInput.value = '';
                }
            }
        });

        $("#submitButton").click(function () {
            saveItems();
        })


        function addItem(barcode) {
            $.ajax({
                url: "{{ url('logScan') }}",
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify({
                    barcode_data: barcode,
                }),

                success: function (response) {
                    if (response.success == 'failed') {
                        // console.error('Error:', response.error);
                        alert("No Data Available");

                    } else {
                        // console.log('Success:', response);
                        // itemList.push(barcode);
                        itemList.push(response.data);
                        updateItemList();
                        alert("Data logged successfully");

                    }
                },
                error: function (xhr, status, error) {
                    $(".messageexist").css({
                        'display': "block"
                    });
                    console.error('Error:', error);
                }
            });

        }


        if (data.message == 'exist') {
            $(".messageexist").css({
                'display': "block"
            });
        }

        function updateItemList() {

            itemListElement.innerHTML = '';

            itemList.forEach((item, index) => {
                const listItem = document.createElement('li');
                const typeItem = document.createElement('span');
                const quantityItem = document.createElement('span');

                typeItem.textContent = item[0].type;
                typeItem.classList.add('badge', 'background-green', 'text-white', 'ml-2');

                quantityItem.textContent = `1x`;
                quantityItem.classList.add('badge', 'background-green', 'text-white', 'ml-2');

                listItem.textContent = `Item ${index + 1}: Serial ( ${item[0].serial} ) : ${item[0].name}`;
                listItem.appendChild(typeItem);
                listItem.appendChild(quantityItem);
                itemListElement.appendChild(listItem);
            });
        }

        function saveItems(response) {

            var shift = $("#inventoryshift").val();
            var receivedby = $("#receivedby").val();
            const typeid = itemList.length > 0 ? itemList[0][0].typeid : null;

            if (!typeid) {
                alert("Type ID is missing.");
                return;
            }

            const formattedItems = itemList.map(item => item[0]);

            $.ajax({
                url: "{{ url('saveItems') }}",
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify({
                    items: formattedItems,
                    shift: shift,
                    created_by: receivedby,
                    type: typeid
                }),
                success: function (data) {
                    console.log('Items saved successfully:', data);
                    // Optionally, you can clear the item list and update the UI
                    itemList = [];
                    updateItemList();
                    alert('Items saved successfully!');
                    // $('#formadd').modal('hide');

                },
                error: function (xhr, status, error) {
                    console.error('Error saving items:', error);
                }
            });
        }
    });

    document.getElementById('printInventory').addEventListener('click', function () {
        var form = document.getElementById('formcheckout');
        form.submit();
    });



    $.ajax({
        type: "GET",
        url: "{{ url('listreceiver')}}",
        dataType: "JSON",
        success: function (html) {
            var objs = html.message;
            jQuery.each(objs, function (index, record) {
                var id = decodeURIComponent(record.id);
                var name = decodeURIComponent(record.fullname);
                $("#receivedby").append($("<option></option>")
                    .attr("value", id)
                    .text(name));
            });
        }
    });



    // function handleBarcode(scanned_barcode) {
    //     document.querySelector('#reader').innerHTML = scanned_barcode;
    // }

    // var barcode = '';
    // var interval;
    // document.addEventListener('keydown', function(evt) {
    //     if (interval)
    //         clearInterval(interval);
    //     if (evt.code == 'Enter') {
    //         if (barcode)
    //             handleBarcode(barcode);
    //         barcode = '';
    //         return;
    //     }
    //     if (evt.code != 'Shift')
    //         barcode += evt.key;
    //     interval = setInterval(() => barcode = '', 20);
    // });






    // function onScanSuccess(decodedText, decodedResult) {
    //     // Handle on success condition with the decoded text or result.
    //     console.log(`Scan result: ${decodedText}`, decodedResult);
    // }

    // var html5QrcodeScanner = new Html5QrcodeScanner(
    //     "reader", {
    //         fps: 10,
    //         qrbox: 250
    //     });
    // html5QrcodeScanner.render(onScanSuccess);

    // var html5QrcodeScanner = new Html5QrcodeScanner(
    //     "reader", {
    //         fps: 10,
    //         qrbox: 250
    //     });

    // function onScanSuccess(decodedText, decodedResult) {
    //     // Handle on success condition with the decoded text or result.
    //     console.log(`Scan result: ${decodedText}`, decodedResult);
    //     // ...
    //     html5QrcodeScanner.clear();
    //     // ^ this will stop the scanner (video feed) and clear the scan area.
    // }

    // html5QrcodeScanner.render(onScanSuccess);

    // function onScanSuccess(decodedText, decodedResult) {
    //     // Handle on success condition with the decoded text or result.
    //     console.log(`Scan result: ${decodedText}`, decodedResult);
    // }

    // function onScanError(errorMessage) {
    //     // handle on error condition, with error message
    // }

    // var html5QrcodeScanner = new Html5QrcodeScanner(
    //     "reader", {
    //         fps: 10,
    //         qrbox: 250
    //     });
    // html5QrcodeScanner.render(onScanSuccess, onScanError);

    (function ($) {



        function assetstatus(status) {
            if (status == 1) {
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
                return "<span class='badge badge-data text-white background-blue'>Out of Repair</span>";

            } else {
                return "<span class='badge badge-data text-white background-gray'>Undefined</span>";
            }
        }

        function historystatus(checkstatus) {
            console.log(checkstatus)
            if (checkstatus == 2) {
                return "<span class='badge badge-data text-white background-red'>Borrowed</span>";
            } else {
                return "<span class='badge badge-data text-white background-blue'>Returned</span>";
            }
        }

        "use strict";
        $('#data').DataTable({
            ajax: "{{ url('inventory')}}",
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
                data: 'assettag',
                defaultContent: "<span class='badge badge-data text-white background-red'>Not Applicable</span>",
                searchable: true,
            },
            {
                data: 'serial',
                searchable: true,

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
            {
                data: function (e) {
                    return assetstatus(e.status);
                },
            },
            {
                data: function (e) {
                    return historystatus(e.checkstatus);
                },
            },
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
                customize: function (doc) {
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
            ],
            drawCallback: function () {
                $('.dataTables_filter input').unbind();
                $('.dataTables_filter input').bind('keyup', function (e) {
                    var code = e.keyCode || e.which;
                    table = $("#data").DataTable();
                    if (code == 13) {
                        table.search(this.value).draw();

                        table.one('xhr', function () {
                            var response = table.ajax.json();
                            var recordsFiltered = response.recordsFiltered;
                            if (recordsFiltered > 0) {
                                $("#messagescansuccess").css('display', "block");
                                $("#messagescaninvalid").css('display', "none");

                                // console.log(table.search(this.value));
                                if (table.search(this.value) !== "") {
                                    window.setTimeout(function () {
                                        $(".btnconfirm").click();
                                        window.setTimeout(function () {
                                            $(".btnbb").click();
                                        }, 1000);
                                    }, 1000);
                                } else {
                                    console.log("goods")
                                }

                                // Attach click event listener to filtered records
                                // $("#data tbody").on("click", ".btnconfirm", function() {
                                //     var d = table.row($(this).closest("tr")).data();
                                //     console.log(d);
                                // });

                            } else {

                                $("#messagescaninvalid").css('display', "block");
                                $("#messagescansuccess").css('display', "none");
                            }
                        });
                    }
                });
            },

        });




        //get all supplier
        $.ajax({
            type: "GET",
            url: "{{ url('listsupplier')}}",
            dataType: "JSON",
            success: function (html) {
                var objs = html.message;
                jQuery.each(objs, function (index, record) {
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

        //get all employee
        $.ajax({
            type: "GET",
            url: "{{ url('listemployees')}}",
            dataType: "JSON",
            success: function (html) {
                var objs = html.message;
                jQuery.each(objs, function (index, record) {
                    var id = decodeURIComponent(record.id);
                    var name = decodeURIComponent(record.fullname);
                    $("#checkinemployeeid").append($("<option></option>")
                        .attr("value", id)
                        .text(name));
                    $("#checkinemployeeid1").append($("<option></option>")
                        .attr("value", id)
                        .text(name));
                    $("#checkoutemployeeid").append($("<option></option>")
                        .attr("value", id)
                        .text(name));
                    $("#checkoutemployeeid1").append($("<option></option>")
                        .attr("value", id)
                        .text(name));
                });
            }
        });


        //get all asset type
        $.ajax({
            type: "GET",
            url: "{{ url('listassettype')}}",
            dataType: "JSON",
            success: function (html) {
                var objs = html.message;
                jQuery.each(objs, function (index, record) {
                    var id = decodeURIComponent(record.id);
                    var name = decodeURIComponent(record.name);
                    $("#typeid").append($("<option></option>")
                        .attr("value", id)
                        .text(name));
                    $("#edittypeid").append($("<option></option>")
                        .attr("value", id)
                        .text(name));
                });
            }
        });


        //get all brand 
        $.ajax({
            type: "GET",
            url: "{{ url('listbrand')}}",
            dataType: "JSON",
            success: function (html) {
                var objs = html.message;
                jQuery.each(objs, function (index, record) {
                    var id = decodeURIComponent(record.id);
                    var name = decodeURIComponent(record.name);
                    $("#brandid").append($("<option></option>")
                        .attr("value", id)
                        .text(name));
                    $("#editbrandid").append($("<option></option>")
                        .attr("value", id)
                        .text(name));
                });
            }
        });

        //get all location 
        $.ajax({
            type: "GET",
            url: "{{ url('listlocation')}}",
            dataType: "JSON",
            success: function (html) {
                var objs = html.message;
                jQuery.each(objs, function (index, record) {
                    var id = decodeURIComponent(record.id);
                    var name = decodeURIComponent(record.name);
                    $("#locationid").append($("<option></option>")
                        .attr("value", id)
                        .text(name));
                    $("#editlocationid").append($("<option></option>")
                        .attr("value", id)
                        .text(name));
                });
            }
        });

        //generate product code
        $.ajax({
            type: "GET",
            url: "{{ url('asset/generateproductcode')}}",
            dataType: "JSON",
            success: function (html) {
                var objs = html.message;
                $("#assettag").val(html.message);
            }
        });

        $("#formedit").validate({
            rules: {
                warranty: {
                    required: true,
                    digits: true,
                    maxlength: 2
                }
            },

            submitHandler: function (form) {
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
                form.append('picture', picture);

                $.ajax({
                    type: "POST",
                    url: "{{ url('updateasset')}}",
                    data: form,
                    contentType: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        console.log(data);
                        if (data.message == 'success') {
                            $("#messageupdate").css({
                                'display': "block"
                            });
                            $('#edit').modal('hide');
                            window.setTimeout(function () {
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

        function showEditModal() {
            // $("#edit").prop('class', 'modal fade');
            if (targetModalEvent) {
                var $modal = $('#edit'),
                    id = $(targetModalEvent.relatedTarget).attr('customdata');
                $.ajax({
                    type: "POST",
                    url: "{{ url('assetbyid') }}",
                    data: {
                        id: id
                    },
                    dataType: "JSON",
                    success: function (data) {
                        $("#editid").val(id);
                        $("#editname").val(data.message.assetname);
                        $("#editlocationid").val(data.message.locationid);
                        $("#editsupplierid").val(data.message.supplierid);
                        $("#editbrandid").val(data.message.brandid);
                        $("#edittypeid").val(data.message.typeid);
                        $("#editassettag").val(data.message.assettag);
                        $("#editserial").val(data.message.serial);
                        $("#editquantity").val(data.message.quantity);
                        $("#editpurchasedate").val(data.message.purchasedate);
                        $("#editcost").val(data.message.cost);
                        $("#editwarranty").val(data.message.warranty);
                        $("#editstatus").val(data.message.status);
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
                    submitHandler: function (form) {
                        $.ajax({
                            method: "POST",
                            url: "{{ url('deleteasset')}}",
                            data: $("#formdelete").serialize(),
                            dataType: "JSON",
                            success: function (data) {
                                console.log(data);
                                $("#messagedelete").css({
                                    'display': "block"
                                });
                                $('#delete').modal('hide');
                                window.setTimeout(function () {
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
                success: function (data) {
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

        $("#edit #delete").on('hide.bs.modal', function () {
            $("#editcontent").css('display', 'none');
        });


        // modals goes here
        // edit data
        $('#edit').on('show.bs.modal', function (e) {
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
        $('#delete').on('show.bs.modal', function (e) {
            x = 2;
            // addmodal();
            eventHolder = 'delete';
            // e.preventDefault();
            // $("#edit").css('display', 'none');

            targetModalEvent = e;
            // $("#password").modal("show");
            $(".passwordcontent1").css('display', 'block');
        });


        $("#passwordsubmit").click(function () {
            submitPassword();
        })

        $("#passwordsubmit1").click(function () {
            submitPassword();
        })




        //checkout
        // $("#formcheckout").validate({
        //     submitHandler: function(form) {
        //         $.ajax({
        //             method: "POST",
        //             url: "{{ url('export')}}",
        //             data: $("#formcheckout").serialize(),
        //             dataType: "JSON",
        //             success: function(data) {
        //                 console.log(data);
        //                 $("#checkoutsuccess").css({
        //                     'display': "block"
        //                 });
        //                 $('#checkout').modal('hide');
        //                 window.setTimeout(function() {
        //                     location.reload()
        //                 }, 2000)
        //             }
        //         });
        //     }
        // });


        //checkin
        $("#formcheckin").validate({
            submitHandler: function (form) {
                $.ajax({
                    method: "POST",
                    url: "{{ url('savecheckin')}}",
                    data: $("#formcheckin").serialize(),
                    dataType: "JSON",
                    success: function (data) {
                        console.log(data);
                        $("#checkinsuccess").css({
                            'display': "block"
                        });
                        $('#checkin').modal('hide');
                        window.setTimeout(function () {
                            location.reload()
                        }, 2000)
                    }
                });
            }
        });

        //show checkout
        $('#checkout').on('show.bs.modal', function (e) {
            var $modal = $(this),
                id = $(e.relatedTarget).attr('customdata');
            $.ajax({
                type: "POST",
                url: "{{ url('assetbyid')}}",
                data: {
                    id: id
                },
                dataType: "JSON",
                success: function (data) {
                    $("#assetid").val(id);
                    $("#checkoutname").val(data.message.name);
                    $("#checkoutassettag").val(data.message.assettag);
                }
            });
        });

        //show checkin
        $('#checkin').on('show.bs.modal', function (e) {
            var $modal = $(this),
                id = $(e.relatedTarget).attr('customdata');
            $.ajax({
                type: "POST",
                url: "{{ url('assetbyid')}}",
                data: {
                    id: id
                },
                dataType: "JSON",
                success: function (data) {
                    $("#checkinassetid").val(id);
                    $("#checkinname").val(data.message.name);
                    $("#checkinassettag").val(data.message.assettag);
                }
            });
        });



        // status change




        // // for scanner auto selection of item





    })(jQuery);
</script>
@endsection