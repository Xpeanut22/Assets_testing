@extends('main')
@section('content')

<section class="">
    <div class="content p-4">
        <div class="row pt-3">
            <div class="col-md-6">
                <h3 class=""><?php echo trans('lang.reportbysupplier');?></h3>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body ">
                        <form action="" method="POST" id="form">
                            <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label><?php echo trans('lang.supplier');?></label>
                                        <select name="supplierid" id="supplierid" class="form-control">
                                            <option value=""><?php echo trans('lang.supplier');?></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2" style="padding-top:33px;">
                                        <button type="submit" class="form-control btn btn-sm btn-fill btn-info"><i class="fa fa-search"></i> <?php echo trans('lang.search');?></button>
                                    </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body ">
                        <div class="table-responsive">
                            <table id="data" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.picture');?></th>
                                        <th><?php echo trans('lang.assettag');?></th>
                                        <th><?php echo trans('lang.serial');?></th>
                                        <th><?php echo trans('lang.purchasedate');?></th>
                                        <th><?php echo trans('lang.cost');?></th>
                                        <th><?php echo trans('lang.description');?></th>
                                        <th><?php echo trans('lang.name');?></th>
                                        <th><?php echo trans('lang.supplier');?></th>
                                        <th><?php echo trans('lang.brand');?></th>
                                        <th><?php echo trans('lang.location');?></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.picture');?></th>
                                        <th><?php echo trans('lang.assettag');?></th>
                                        <th><?php echo trans('lang.serial');?></th>
                                        <th><?php echo trans('lang.purchasedate');?></th>
                                        <th><?php echo trans('lang.cost');?></th>
                                        <th><?php echo trans('lang.description');?></th>
                                        <th><?php echo trans('lang.name');?></th>
                                        <th><?php echo trans('lang.supplier');?></th>
                                        <th><?php echo trans('lang.brand');?></th>
                                        <th><?php echo trans('lang.location');?></th>
                                    </tr>
                                </tfoot>
                               
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<script>
(function($) {
"use strict";  
    //get all supplier
    $.ajax({
        type: "GET",
        url: "{{ url('listsupplier')}}",
        dataType: "JSON",
        success: function(html) {
            var objs = html.message;
            jQuery.each(objs, function (index, record) {
                var id = decodeURIComponent(record.id);
                var name = decodeURIComponent(record.name);
                $("#supplierid").append($("<option></option>").attr("value",id)
                    .text(name));
                
            });
        }   
    }); 

    var tabledata = $('#data').DataTable({
        bFilter : false,
        ajax: {
                url : "{{ url('getdatabysupplierreport')}}",
                data: function (d) {
                    d.supplierid = $("#supplierid").val();
                },
            },
       
        columns: [{
            data: 'id',
                orderable: false,
                searchable: false,
                visible: false,
                name:'id'
            },
            
            {
                data: 'pictures',
                name:'pictures'
            },
            {
                data: 'assettag',
                name: 'assettag'
            },
            {
             data: 'serial',
                orderable: false,
                searchable: false,
                visible: false,
                name: 'serial',
            },
            {
                data: 'purchasedate',
                orderable: false,
                searchable: false,
                visible: false,
                name: 'purchasedate',
            },
            {data: 'cost',
                orderable: false,
                searchable: false,
                visible: false,
                name: 'cost',
            },
           
            {
             data: 'description',
                orderable: false,
                searchable: false,
                visible: false,
                name: 'description',
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'supplier',
                name: 'supplier'
            },
            {
                data: 'brand',
                name: 'brand'
            },
            {
                data: 'location',
                name: 'location'
            }
        ],
        dom: "<'row'<'col-sm-9 text-left'B><'col-sm-3'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-2'l><'col-sm-5'i><'col-sm-5'p>>",
        buttons: [{
                extend: 'copy',
                text: 'Copy <i class="fa fa-files-o"></i>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: '<?php echo trans('lang.reportbysupplier ');?>',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5,6, 7, 8, 9, 10]
                }
            },
            {
                extend: 'csv',
                text: 'CSV <i class="fa fa-file-excel-o"></i>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: '<?php echo trans('lang.reportbysupplier');?>',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5,6, 7, 8, 9, 10]
                }
            },
            {
                extend: 'pdf',
                text: 'PDF <i class="fa fa-file-pdf-o"></i>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: '<?php echo trans('lang.reportbysupplier');?>',
                orientation: 'landscape',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5,6, 7, 8, 9, 10]
                },
                customize: function(doc) {
                    doc.styles.tableHeader.alignment = 'left';
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
                        .join('*').split('');
                }
            },
            {
                extend: 'print',
                title: '<?php echo trans('lang.reportbysupplier');?>',
                className: 'btn btn-sm btn-fill btn-info ',
                text: 'Print <i class="fa fa-print"></i>',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5,6, 7, 8, 9, 10]
                }
            }
        ]
    });
    
    //do filter
    
    
    //do search
    $('#form').on('submit', function(e) {
        tabledata.draw();
        e.preventDefault();
    });

})(jQuery);
</script>
@endsection