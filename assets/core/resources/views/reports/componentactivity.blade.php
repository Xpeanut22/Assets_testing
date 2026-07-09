@extends('main')
@section('content')

<section class="">
    <div class="content p-4">
        <div class="row pt-3">
            <div class="col-md-6">
                <h3 class=""><?php echo trans('lang.issueactivity');?></h3>
            </div>
        </div>
       
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body ">
                        <div class="table-responsive">
                            <table id="recentcomponentactivity" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.name');?></th>
                                        <th><?php echo trans('lang.serial');?></th>
                                        <th><?php echo trans('lang.controlno');?></th>
                                        <th><?php echo trans('lang.quantity');?></th>
                                        <th><?php echo trans('lang.issueto');?></th>
                                        <th><?php echo trans('lang.mobileno');?></th>
                                        <th><?php echo trans('lang.location');?></th>
                                        <th><?php echo trans('lang.issuancetype');?></th>
                                        <th><?php echo trans('lang.createby');?></th>
                                        <th><?php echo trans('lang.date');?></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.name');?></th>
                                        <th><?php echo trans('lang.serial');?></th>
                                        <th><?php echo trans('lang.controlno');?></th>
                                        <th><?php echo trans('lang.quantity');?></th>
                                        <th><?php echo trans('lang.issueto');?></th>
                                        <th><?php echo trans('lang.mobileno');?></th>
                                        <th><?php echo trans('lang.location');?></th>
                                        <th><?php echo trans('lang.issuancetype');?></th>
                                        <th><?php echo trans('lang.createby');?></th>
                                        <th><?php echo trans('lang.date');?></th>
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

"use strict";  
    $('#recentcomponentactivity').DataTable({

        ajax: "{{ url('home/recentcomponentactivity')}}",
       
        columns: [{
            data: 'id',
                orderable: false,
                searchable: false,
                visible: false
            },
            {
                data: 'component'
            },
            {
                data: 'cserial'
            },
            {
                data: 'ccontrolno'
            },
            {
                data: 'quantity'
            },
            // {
            //     data: 'status'
            // },
            {
                data: 'employees'
            },
            {
                data: 'mobile_number'
            },
            {
                data: 'departmentname'
            },
            {
                    data: function(e) {
                        return issuetype(e.issuancetype);
                    },
                },
           
            {
                data: 'rfullname'
            },
            {
                data: 'date'
            }
        ],
        dom: "<'row'<'col-sm-9 text-left'B><'col-sm-3'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-2'l><'col-sm-5'i><'col-sm-5'p>>",
        buttons: [{
                extend: 'copy',
                text: 'Copy <i class="fa fa-files-o"></i>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: '<?php echo trans('lang.componentactivity ');?>',
                exportOptions: {
                    columns: [1, 2, 3, 4 ,5, 6, 7, 8]
                }
            },
            {
                extend: 'csv',
                text: 'CSV <i class="fa fa-file-excel-o"></i>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: '<?php echo trans('lang.componentactivity');?>',
                exportOptions: {
                    columns: [1, 2, 3, 4 ,5, 6, 7, 8]

                }
            },
            {
                extend: 'pdf',
                text: 'PDF <i class="fa fa-file-pdf-o"></i>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: '<?php echo trans('lang.componentactivity');?>',
                orientation: 'landscape',
                exportOptions: {
                    columns: [1, 2, 3, 4 ,5, 6, 7, 8]

                },
                customize: function(doc) {
                    doc.styles.tableHeader.alignment = 'left';
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
                        .join('*').split('');
                }
            },
            {
                extend: 'print',
                title: '<?php echo trans('lang.componentactivity');?>',
                className: 'btn btn-sm btn-fill btn-info ',
                text: 'Print <i class="fa fa-print"></i>',
                exportOptions: {
                    columns: [1, 2, 3, 4 ,5, 6, 7, 8]

                }
            }
        ]
    });

})(jQuery);
</script>
@endsection