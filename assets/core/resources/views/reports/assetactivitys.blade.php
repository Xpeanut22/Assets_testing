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
                        <form action="" method="POST" id="form">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label><?php echo trans('lang.status'); ?></label>
                                    <select name="statusfilter" id="statusfilter" class="form-control">
                                        <option value="">All</option>
                                        <option value="1">Borrowed</option>
                                        <option value="2">Returned</option>
                                        <option value="3">Serviceable</option>
                                        <option value="4">Unserviceable</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Report Type</label>
                                    <select name="asset_scope" id="asset_scope" class="form-control">
                                        <option value="">All</option>
                                        <option value="assets">Assets</option>
                                        <option value="vehicle">Assets Vehicle</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2" style="padding-top:33px;">
                                    <button type="submit" class="form-control btn btn-sm btn-fill btn-info"><i class="fa fa-search"></i> <?php echo trans('lang.search'); ?></button>
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
        var tabledata = $('#recentassetactivity').DataTable({
            ajax: {
                url: "{{ url('listassetactivityreport')}}",
                data: function(d) {
                    d.status = $("#statusfilter").val();
                    d.asset_scope = $("#asset_scope").val();
                }
            },
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
                    text: 'PDF <i class="fa fa-file-pdf-o"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    action: function() {
                        openActivityPdf();
                    }
                },
                {
                    text: 'Print <i class="fa fa-print"></i>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    action: function() {
                        openActivityPdf();
                    }
                }
            ]
        });

        function openActivityPdf() {
            var params = $.param({
                status: $("#statusfilter").val(),
                asset_scope: $("#asset_scope").val(),
                search: tabledata.search()
            });
            window.open("{{ url('/reports/assetactivity/print/asset-activity-report') }}?" + params, '_blank');
        }

        $('#form').on('submit', function(e) {
            tabledata.draw();
            e.preventDefault();
        });

    })(jQuery);
</script>
@endsection
