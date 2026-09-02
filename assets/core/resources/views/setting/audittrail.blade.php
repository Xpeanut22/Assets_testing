@extends('main')
@section('content')
<style>
    .audit-trail-page,
    .audit-trail-page table {
        font-size: 14px;
    }

    .audit-trail-page .table th,
    .audit-trail-page .table td {
        padding: 14px 18px;
        vertical-align: middle;
        font-weight: 700;
    }

    .audit-trail-page .badge {
        min-width: 72px;
        padding: 8px 14px;
        border-radius: 18px;
        font-size: 12px;
    }

    .audit-detail-modal .modal-dialog {
        max-width: 1000px;
    }

    .audit-detail-modal .modal-title {
        font-size: 28px;
        font-weight: 700;
    }

    .audit-detail-modal .modal-body {
        font-size: 18px;
        color: #5f5a55;
    }

    .audit-detail-row {
        display: grid;
        grid-template-columns: 230px 1fr;
        gap: 20px;
        margin-bottom: 12px;
        font-weight: 700;
    }

    .audit-detail-label {
        color: #677082;
    }

    .audit-values-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 36px;
        margin-top: 26px;
    }

    .audit-values-title {
        font-size: 24px;
        font-weight: 500;
        margin-bottom: 20px;
    }

    .audit-values-box {
        min-height: 125px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 24px;
        font-weight: 700;
    }

    .audit-values-old {
        background: #fff0f2;
    }

    .audit-values-new {
        background: #effff4;
    }

    .audit-value-key {
        color: #677082;
        font-size: 14px;
        text-transform: uppercase;
    }

    .audit-value-item + .audit-value-item {
        border-top: 1px solid rgba(0,0,0,.08);
        margin-top: 14px;
        padding-top: 14px;
    }

    .audit-no-values {
        color: #9ca3af;
        font-style: italic;
    }

    .audit-filter-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .audit-filter-bar label {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        color: #677082;
    }

    .audit-filter-bar input[type="date"] {
        min-width: 150px;
        height: 34px;
        padding: 6px 10px;
        border: 1px solid #d9d9d9;
        border-radius: 4px;
        font-size: 13px;
    }

    .audit-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .audit-toolbar-right {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        margin-left: auto;
    }

    .audit-toolbar .dataTables_filter {
        margin: 0;
    }
</style>

<section class="">
    <div class="content p-4 audit-trail-page">
        <div class="row pt-3">
            <div class="col-md-8">
                <h3 class="">Audit Trail</h3>
                <p class="text-muted mb-0">Read-only, traceable history of authentication and system data changes.</p>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table id="data" class="table table-striped" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Log #</th>
                                    <th>User</th>
                                    <th>Module</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>Date &amp; Time</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade audit-detail-modal" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Audit Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="audit-detail-row">
                    <div class="audit-detail-label">User</div>
                    <div id="detailUser"></div>
                </div>
                <div class="audit-detail-row">
                    <div class="audit-detail-label">Module</div>
                    <div id="detailModule"></div>
                </div>
                <div class="audit-detail-row">
                    <div class="audit-detail-label">Action</div>
                    <div id="detailAction"></div>
                </div>
                <div class="audit-detail-row">
                    <div class="audit-detail-label">Entity Type</div>
                    <div id="detailEntityType"></div>
                </div>
                <div class="audit-detail-row">
                    <div class="audit-detail-label">Entity ID</div>
                    <div id="detailEntityId"></div>
                </div>
                <div class="audit-detail-row">
                    <div class="audit-detail-label">Details</div>
                    <div id="detailText" style="white-space:pre-line;"></div>
                </div>
                <div class="audit-detail-row">
                    <div class="audit-detail-label">Date &amp; Time</div>
                    <div id="detailDate"></div>
                </div>

                <div class="audit-values-grid">
                    <div>
                        <div class="audit-values-title">Old Values</div>
                        <div id="oldValues" class="audit-values-box audit-values-old"></div>
                    </div>
                    <div>
                        <div class="audit-values-title">New Values</div>
                        <div id="newValues" class="audit-values-box audit-values-new"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
(function($) {
"use strict";

    var table = $('#data').DataTable({
        ajax: {
            url: "{{ url('audittrail') }}",
            data: function(d) {
                d.datefrom = $('#auditDateFrom').val();
                d.dateto = $('#auditDateTo').val();
            }
        },
        order: [[0, 'desc']],
        columns: [
            { data: 'log_number', name: 'id' },
            { data: 'user', name: 'user_name' },
            { data: 'module', name: 'module' },
            { data: 'action_badge', name: 'action' },
            { data: 'details_link', name: 'details', orderable: false, searchable: false },
            { data: 'date_time', name: 'created_at' }
        ],
        dom: 'Bfrtip',
        buttons: [
            'copy',
            'csv',
            {
                text: 'PDF',
                action: function() {
                    window.open(buildAuditPrintUrl(), '_blank');
                }
            },
            {
                text: 'Print',
                action: function() {
                    window.open(buildAuditPrintUrl(), '_blank');
                }
            }
        ]
    });

    var $toolbar = $('<div class="audit-toolbar"></div>');
    var $toolbarRight = $('<div class="audit-toolbar-right"></div>');
    var $filterBar = $(
        '<div class="audit-filter-bar">' +
            '<label for="auditDateFrom">From</label>' +
            '<input type="date" id="auditDateFrom">' +
            '<label for="auditDateTo">To</label>' +
            '<input type="date" id="auditDateTo">' +
        '</div>'
    );

    $('#data_wrapper .dt-buttons').before($toolbar);
    $toolbar.append($('#data_wrapper .dt-buttons'));
    $toolbar.append($toolbarRight);
    $toolbarRight.append($filterBar);
    $toolbarRight.append($('#data_wrapper .dataTables_filter'));

    function buildAuditPrintUrl() {
        var params = [];
        var dateFrom = $('#auditDateFrom').val();
        var dateTo = $('#auditDateTo').val();

        if (dateFrom) {
            params.push('datefrom=' + encodeURIComponent(dateFrom));
        }
        if (dateTo) {
            params.push('dateto=' + encodeURIComponent(dateTo));
        }

        var baseUrl = "{{ url('audittrail/print/audit-trail-report') }}";
        return params.length ? baseUrl + '?' + params.join('&') : baseUrl;
    }

    function parseValues(value) {
        if (!value) {
            return null;
        }
        if (typeof value === 'object') {
            return value;
        }
        try {
            return JSON.parse(value);
        } catch (e) {
            return null;
        }
    }

    $('#auditDateFrom, #auditDateTo').on('change', function() {
        table.ajax.reload();
    });

    function renderValues(value) {
        var parsed = parseValues(value);
        if (!parsed || $.isEmptyObject(parsed)) {
            return '<div class="audit-no-values">No values</div>';
        }

        var html = '';
        $.each(parsed, function(key, val) {
            html += '<div class="audit-value-item">'
                + '<div class="audit-value-key">' + escapeHtml(key) + '</div>'
                + '<div>' + escapeHtml(val === null || val === '' ? '-' : val) + '</div>'
                + '</div>';
        });
        return html;
    }

    function escapeHtml(value) {
        return $('<div>').text(value).html();
    }

    function formatDateTime(value) {
        if (!value) {
            return '-';
        }

        var parts = String(value).split(/[- :]/);
        if (parts.length >= 6) {
            var date = new Date(parts[0], parts[1] - 1, parts[2], parts[3], parts[4], parts[5]);
            return date.toLocaleString('en-US', {
                month: 'short',
                day: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        return value;
    }

    $(document).on('click', '.audit-details', function(e) {
        e.preventDefault();

        $('#detailUser').text('');
        $('#detailModule').text('');
        $('#detailAction').text('');
        $('#detailEntityType').text('');
        $('#detailEntityId').text('');
        $('#detailDate').text('');
        $('#detailText').text('');
        $('#oldValues').empty();
        $('#newValues').empty();
        $('.audit-values-grid').hide();

        $.ajax({
            type: "POST",
            url: "{{ url('audittrailbyid') }}",
            data: {
                id: $(this).data('id'),
                _token: "{{ csrf_token() }}"
            },
            dataType: "JSON",
            success: function(data) {
                if (data.success === 'success') {
                    var row = data.message;
                    $('#detailUser').text(row.user_name || '-');
                    $('#detailModule').text(row.module || '-');
                    $('#detailAction').text(row.action || '-');
                    $('#detailEntityType').text(row.entity_type || row.module || '-');
                    $('#detailEntityId').text(row.entity_id || '-');
                    $('#detailDate').text(row.date_time || formatDateTime(row.created_at));
                    $('#detailText').text(row.details || 'No details.');

                    var action = (row.action || '').trim().toLowerCase();
                    var shouldHideValues = (
                        action === 'scan' ||
                        action === 'login' ||
                        action === 'logout'
                    );

                    if (!shouldHideValues) {
                        $('#oldValues').html(renderValues(row.old_values));
                        $('#newValues').html(renderValues(row.new_values));
                        $('.audit-values-grid').show();
                    } else {
                        $('.audit-values-grid').hide();
                    }

                    $('#detailModal').modal('show');
                }
            }
        });
    });

    $('#detailModal').on('hidden.bs.modal', function() {
        $('#detailUser').text('');
        $('#detailModule').text('');
        $('#detailAction').text('');
        $('#detailEntityType').text('');
        $('#detailEntityId').text('');
        $('#detailDate').text('');
        $('#detailText').text('');
        $('#oldValues').empty();
        $('#newValues').empty();
        $('.audit-values-grid').hide();
    });

})(jQuery);
</script>
@endsection
