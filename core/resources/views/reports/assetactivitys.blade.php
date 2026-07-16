@extends('main')
@section('content')

<section class="">
    <div class="content p-4">
        <div class="row pt-3">
            <div class="col-md-6">
                <h3 class=""><?php echo trans('lang.assetactivity'); ?></h3>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body ">
                        <div class="table-responsive">
                            <table id="recentassetactivity" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.asset'); ?></th>
                                        <th><?php echo trans('lang.assettag'); ?></th>
                                        <th>Name</th>
                                        <th><?php echo trans('lang.status'); ?></th>
                                        <th><?php echo trans('lang.itemstatus'); ?></th>
                                        <th><?php echo trans('lang.location'); ?></th>
                                        <th><?php echo trans('lang.createby'); ?></th>
                                        <th><?php echo trans('lang.date'); ?></th>
                                    </tr>
                                </thead>
                                <!-- <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.asset'); ?></th>
                                        <th><?php echo trans('lang.assettag'); ?></th>
                                        <th>Name</th>
                                        <th><?php echo trans('lang.status'); ?></th>
                                        <th><?php echo trans('lang.itemstatus'); ?></th>
                                        <th><?php echo trans('lang.location'); ?></th>
                                        <th><?php echo trans('lang.createby'); ?></th>
                                        <th><?php echo trans('lang.date'); ?></th>
                                    </tr>
                                </tfoot> -->
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

        function assetstatus(itemstatus, historyStatus) {
            if (historyStatus == 4) {
                return "<span class='badge badge-data text-white background-red'>Unserviceable</span>";
            } else if (historyStatus == 1) {
                return "<span class='badge badge-data text-white background-green'>Serviceable</span>";
            } else if (historyStatus == 2 || historyStatus == 3) {
                return "<span class='badge badge-data text-white background-green'>Ready to Deploy</span>";
            } else if (itemstatus == 1) {
                return "<span class='badge badge-data text-white background-green'>Ready to Deploy</span>";
            } else if (itemstatus == 2) {
                return "<span class='badge badge-data text-white background-yellow'>Pending</span>";

            } else if (itemstatus == 3) {
                return "<span class='badge badge-data text-white background-orange'>Archived</span>";

            } else if (itemstatus == 4) {
                return "<span class='badge badge-data text-white background-red'>Broken</span>";

            } else if (itemstatus == 5) {
                return "<span class='badge badge-data text-white background-black'>Lost</span>";

            } else if (itemstatus == 6) {
                return "<span class='badge badge-data text-white background-red'>Unserviceable</span>";

            } else {
                return "<span class='badge badge-data text-white background-gray'>Undefined</span>";
            }
        }

        "use strict";
        $('#recentassetactivity').DataTable({
            ajax: "{{ url('listassetactivityreport')}}",
            columns: [{
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    visible: false
                },
                {
                    data: 'asset'
                },
                {
                    data: 'tag'
                },
                {
                    data: 'employees'
                },
                {
                    data: 'status'
                },
                {
                    data: function(e) {
                        return assetstatus(e.itemstatus, e.historystatus);
                    },
                },
                {
                    data: 'location'
                },
                {
                    data: 'fullname'
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
                    title: '<?php echo trans('lang.assetactivity '); ?>',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]

                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV <i class="fa fa-file-excel-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.assetactivity'); ?>',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF <i class="fa fa-file-pdf-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: '<?php echo trans('lang.assetactivity'); ?>',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]

                    },
                    customize: function(doc) {
                        doc.styles.tableHeader.alignment = 'left';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
                            .join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: '<?php echo trans('lang.assetactivity'); ?>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    text: 'Print <i class="fa fa-print"></i>',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]

                        
                    }
                }
            ]
        });

    })(jQuery);
</script>
@endsection
