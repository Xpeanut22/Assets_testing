@extends('main')
@section('content')

<section class="">
    <div class="content p-4">
        <div class="row pt-3">
            <div class="col-md-6">
                <h3 class=""><?php echo trans('lang.maintenancereport');?></h3>
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
                                        <th><?php echo trans('lang.assettag');?></th>
                                        <th><?php echo trans('lang.asset');?></th>
                                        <th><?php echo trans('lang.supplier');?></th>
                                        <th><?php echo trans('lang.type');?></th>
                                        <th><?php echo trans('lang.startdate');?></th>
                                        <th><?php echo trans('lang.enddate');?></th>
                                        <th><?php echo trans('lang.completionday');?></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.assettag');?></th>
                                        <th><?php echo trans('lang.asset');?></th>
                                        <th><?php echo trans('lang.supplier');?></th>
                                        <th><?php echo trans('lang.type');?></th>
                                        <th><?php echo trans('lang.startdate');?></th>
                                        <th><?php echo trans('lang.enddate');?></th>
                                        <th><?php echo trans('lang.completionday');?></th>
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

</section>

<script>
(function($) {
"use strict";  
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
            {
                data: 'supplier'
            },
            {
                data: 'type'
            },
            {
                data: 'startdate'
            },
            {
                data: 'enddate'
            },
             {
                data: 'completion'
            },
           
        ],
        dom: "<'row'<'col-sm-9 text-left'B><'col-sm-3'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-2'l><'col-sm-5'i><'col-sm-5'p>>",
        buttons: [{
                extend: 'copy',
                text: 'Copy <i class="fa fa-files-o"></i>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: '<?php echo trans('lang.maintenancereport ');?>',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5,6, 7]
                }
            },
            {
                extend: 'csv',
                text: 'CSV <i class="fa fa-file-excel-o"></i>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: '<?php echo trans('lang.maintenancereport');?>',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5,6, 7]
                }
            },
            {
                extend: 'pdf',
                text: 'PDF <i class="fa fa-file-pdf-o"></i>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: '<?php echo trans('lang.maintenancereport');?>',
                orientation: 'landscape',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5,6, 7]
                },
                customize: function(doc) {
                    doc.styles.tableHeader.alignment = 'left';
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
                        .join('*').split('');
                }
            },
            {
                extend: 'print',
                title: '<?php echo trans('lang.maintenancereport');?>',
                className: 'btn btn-sm btn-fill btn-info ',
                text: 'Print <i class="fa fa-print"></i>',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5,6, 7]
                }
            }
        ]
    });

})(jQuery);
</script>
@endsection