@extends('main')
@section('content')

<section class="pt-3">
    <div class="p-4">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mb-4"><?php echo trans('lang.dashboard');?></a></h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="row">
                                    <div class="col-md-4 ">
                                        <img width="60" src="<?php echo asset('images/icon-in.png')?>" />
                                    </div>
                                    <div class="col-md-8 pl-4">
                                        <img width="45" class="icon-loading text-left"
                                            src="<?php echo asset('images/icon-loading.gif')?>" />
                                        <h2 class="text-left dailyvisitor"></h2>
                                    </div>

                                </div>
                                <p class="text-left mt-2 mb-0"><?php echo trans('lang.dailyvisitor');?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="row">
                                    <div class="col-md-4 ">
                                        <img width="60" src="<?php echo asset('images/icon-thismonth.png')?>" />
                                    </div>
                                    <div class="col-md-8 pl-4">
                                        <img width="45" class="icon-loading text-left"
                                            src="<?php echo asset('images/icon-loading.gif')?>" />
                                        <h2 class="text-left monthlyvisitor"></h2>
                                    </div>

                                </div>
                                <p class="text-left  mt-2 mb-0"><?php echo trans('lang.monthlyvisitor');?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="row">
                                    <div class="col-md-4 ">
                                        <img width="60" src="<?php echo asset('images/icon-monthly.png')?>" />
                                    </div>
                                    <div class="col-md-8 pl-4">
                                        <img width="45" class="icon-loading text-left"
                                            src="<?php echo asset('images/icon-loading.gif')?>" />
                                        <h2 class="text-left lastmonthvisitor"></h2>
                                    </div>

                                </div>
                                <p class="text-left  mt-2 mb-0"><?php echo trans('lang.lastmonthvisitor');?></p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row pt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mb-4"><?php echo trans('lang.totalvisitinvsout');?></a></h5>
                                        <canvas id="totalvisitors" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card ads-right">
                    <div class="card-body">
                        <h5 class="mb-4"><?php echo trans('lang.totalvisitinvsoutbyhost');?></a></h5>
                        <canvas id="visitorbyhost" height="100"></canvas>
                    </div>
                </div>
                <div class="card mt-3 ">
                    <div class="card-body">
                        <h5 class="mb-4"><?php echo trans('lang.totalvisitinvsoutbypurpose');?></a></h5>
                        <canvas id="visitorbypurpose" height="100"></canvas>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="row pt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mb-4"><?php echo trans('lang.latestvisitor');?></a></h5>
                                        <div id="messageapprove" style="display:none" class="alert alert-success"><?php echo trans('lang.data_approve');?></div>
                                        <div class="table-responsive">
                                            <table id="data" class="table table-striped table-bordered" cellspacing="0"
                                                >
                                                <thead>
                                                    <tr>
                                                        <th>Employee ID</th>
                                                        <th><?php echo trans('lang.profile');?></th>
                                                        <th><?php echo trans('lang.firstname');?></th>
                                                        <th><?php echo trans('lang.lastname');?></th>
                                                        <th><?php echo trans('lang.type');?></th>
                                                        <th><?php echo trans('lang.host');?></th>
                                                        <th><?php echo trans('lang.checkintime');?></th>
                                                        <th><?php echo trans('lang.checkout');?></th>
                                                        <th><?php echo trans('lang.status');?></th>
                                                        <th><?php echo trans('lang.action');?></th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>Employee ID</th>
                                                        <th><?php echo trans('lang.profile');?></th>
                                                        <th><?php echo trans('lang.firstname');?></th>
                                                        <th><?php echo trans('lang.lastname');?></th>
                                                        <th><?php echo trans('lang.type');?></th>
                                                        <th><?php echo trans('lang.host');?></th>
                                                        <th><?php echo trans('lang.checkintime');?></th>
                                                        <th><?php echo trans('lang.checkout');?></th>
                                                        <th><?php echo trans('lang.status');?></th>
                                                        <th><?php echo trans('lang.action');?></th>
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
                </div>
    </div>

    <!--approve data -->
    <div class="modal fade" id="approve" role="dialog">
        <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="#" id="formapprove">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo trans('lang.approve');?></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p><?php echo trans('lang.approve_confirmation');?></p>
                    <input type="hidden" value="" name="id" id="idapprove"/>
            
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="approve"><?php echo trans('lang.approve');?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close');?></button>
                </div>
            </form>   
        </div>
        </div>
    </div>
    <!--end approve data -->

</section>

<script>


//get latest data
$('#data').DataTable({

processing: true,
serverSide: true, 
'bInfo': false,
'bFilter': false,
'pageLength': 5,
'lengthChange': false,
ajax: "{{ url('api/transaction')}}",
"language": {
    "decimal": "",
    "emptyTable": "<?php echo trans('lang.demptyTable');?>",
    "info": "<?php echo trans('lang.dshowing');?> _START_ <?php echo trans('lang.dto');?> _END_ <?php echo trans('lang.dof');?> _TOTAL_ <?php echo trans('lang.dentries');?>",
    "infoEmpty": "<?php echo trans('lang.dinfoEmpty');?>",
    "infoFiltered": "(<?php echo trans('lang.dfilter');?> _MAX_ <?php echo trans('lang.total');?> <?php echo trans('lang.dentries');?>)",
    "infoPostFix": "",
    "thousands": ",",
    "lengthMenu": "<?php echo trans('lang.dshow');?> _MENU_ <?php echo trans('lang.dentries');?>",
    "loadingRecords": "<?php echo trans('lang.dloadingRecords');?>",
    "processing": "<?php echo trans('lang.dprocessing');?>",
    "search": "<?php echo trans('lang.dsearch');?>",
    "zeroRecords": "<?php echo trans('lang.dzeroRecords');?>",
    "paginate": {
        "first": "<?php echo trans('lang.dfirst');?>",
        "last": "<?php echo trans('lang.dlast');?>",
        "next": "<?php echo trans('lang.dnext');?>",
        "previous": "<?php echo trans('lang.dprevious');?>"
    }
},
columns: [{
        data: 'id',
        orderable: false,
        searchable: false,
        visible: false
    },
    {
        data: 'profile'
    },
    {
        data: 'fvisitor'
    },
    {
        data: 'lvisitor'
    },
    {
        data: 'type'
    },
    {
        data: 'fhost'
    },
    {
        data: 'checkintime'
    },
    {
        data: 'check_out'
    },
    {
        data: 'status'
    },
    {
        data: 'action',
        orderable: false,
        searchable: false
    }
]

});


var randomColorGenerator = function () { 
    return '#' + (Math.random().toString(16) + '0000000').slice(2, 8); 
};


//visitor by host
$.ajax({
    type: "GET",
    url: "{{ url('api/visitorbyhost')}}",
    dataType: "json",
    success: function(data) {
        var label = [];
        var amount = [];
        for (var i in data) {
            label.push(data[i].fname + ' ' + data[i].lname);
            amount.push(data[i].amount)
        }
        var cvisitorbyhost = document.getElementById("visitorbyhost");
        var visitorbyhost = new Chart(cvisitorbyhost, {
            type: 'doughnut',
            legendPosition: 'bottom',
            data: {
                labels: label,
                datasets: [{
                    label: "<?php echo trans('lang.host');?>",
                    data: amount,
                    backgroundColor: randomColorGenerator(),
                    borderWidth: 0
                }]
            },
            options: {
                legend: {
                    position: 'bottom',
                },

            }
        });
    }
});

//visitor by purpose
$.ajax({
    type: "GET",
    url: "{{ url('api/visitorbypurpose')}}",
    dataType: "json",
    success: function(data) {
        var label = [];
        var amount = [];
        for (var i in data) {
            label.push(data[i].name);
            amount.push(data[i].amount)
        }
        var cvisitorbypurpose = document.getElementById("visitorbypurpose");
        var visitorbypurpose = new Chart(cvisitorbypurpose, {
            type: 'doughnut',
            legendPosition: 'bottom',
            data: {
                labels: label,
                datasets: [{
                    label: "<?php echo trans('lang.purpose');?>",
                    data: amount,
                    backgroundColor: randomColorGenerator(),
                    borderWidth: 0
                }]
            },
            options: {
                legend: {
                    position: 'bottom',
                },

            }
        });
    }
});

//Visitor in vs out
$.ajax({
    type: "GET",
    url: "{{ url('api/visitorinvsout')}}",
    dataType: "json",
    data: "{}",
    success: function(data) {
        var ctotalvisitors = document.getElementById("totalvisitors");
        var totalvisitors = new Chart(ctotalvisitors, {
            type: 'bar',
            legendPosition: 'bottom',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
                    "Nov", "Dec"
                ],
                datasets: [{
                    label: "<?php echo trans('lang.visitor');?>",
                    data: [data.ijan, data.ifeb, data.imar, data.iapr, data.imay, data.ijun,
                        data.ijul, data.iags, data.isep, data.iokt, data.inov, data.ides
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                pieceLabel: {
                    // render 'label', 'value', 'percentage' or custom function, default is 'percentage'
                    render: 'label'
                },
                legend: {
                    position: 'bottom',
                },

                hover: {
                    mode: 'nearest',
                    intersect: true
                },

            }
        });

    },
});

//get all stats
$.ajax({
        ype: "GET",
		url: "{{ url('api/visitorstats')}}",
        dataType: "JSON",
        beforeSend: function(){
            $(".icon-loading").css('display','block');
        },
		success: function(data) {
            $(".icon-loading").css('display','none');
            $(".dailyvisitor").html(data.daily);
            $(".monthlyvisitor").html(data.month);
            $(".lastmonthvisitor").html(data.lastmonth);
		}   
});  


$("#formapprove").validate({
    submitHandler: function(form) {
        $.ajax({
			method: "POST",
            url: "{{ url('api/approvetransaction')}}",
            data: $("#formapprove").serialize(),
            dataType: "JSON",
            success: function(data) {
                console.log(data);
				$("#messageapprove").css({'display':"block"});
				$('#approve').modal('hide');
				window.setTimeout(function(){location.reload()},2000)
            }
		});
    }
});

//show delete data
$('#approve').on('show.bs.modal', function(e) {
    var $modal = $(this),
    id = $(e.relatedTarget).attr('customdata');
    $("#idapprove").val(id);
});
</script>
@endsection