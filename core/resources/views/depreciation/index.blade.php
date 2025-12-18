@extends('main')
@section('content')

<section class="">
    <div class="content p-4">
        <div class="row pt-3">
            <div class="col-md-6">
                <h3 class=""><?php echo trans('lang.depreciation_list');?></h3>
            </div>
            <div class="col-md-6 text-md-right pb-md-0 pb-3">
            <button type="button" data-toggle="modal" data-target="#add" class="btn btn-sm btn-fill btn-primary"><i class="fa fa-plus"></i> <?php echo trans('lang.add_data');?></button>
            </div>
        </div>
       
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body ">
                    <div id="messagesuccess"  class="display-none alert alert-success"><?php echo trans('lang.data_added');?></div>
					<div id="messagedelete"  class="display-none alert alert-success"><?php echo trans('lang.data_deleted');?></div>
					<div id="messageupdate"  class="display-none alert alert-success"><?php echo trans('lang.data_updated');?></div>
                        <div class="table-responsive">
                            <table id="data" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.name');?></th>
                                        <th><?php echo trans('lang.cost');?></th>
                                        <th><?php echo trans('lang.period');?></th>
                                        <th><?php echo trans('lang.category');?></th>
                                        <th><?php echo trans('lang.asset_value');?></th>
                                        <th><?php echo trans('lang.action');?></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo trans('lang.name');?></th>
                                        <th><?php echo trans('lang.cost');?></th>
                                        <th><?php echo trans('lang.period');?></th>
                                        <th><?php echo trans('lang.category');?></th>
                                        <th><?php echo trans('lang.asset_value');?></th>
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

    <!--add new data -->
    <div id="add" class="modal fade" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
            
                
                <form action="#" id="formadd">
                    <div class="modal-header">
                       
                        <h5 class="modal-title"><?php echo trans('lang.add_data');?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    <div class="messagehigher display-none alert alert-success"><?php echo trans('lang.assetvaluecannothigh');?></div>
                        <div class="form-group">
                            <label><?php echo trans('lang.category');?></label>
                            <select name="category" type="text" id="category" class=" form-control" required>
                                <option value=""><?php echo trans('lang.category');?></option>
                                <option value="1"><?php echo trans('lang.asset');?></option>
                                <option value="2"><?php echo trans('lang.component');?></option>
                            </select>
                        </div>
                        <div class="asset-sec form-group d-none">
                            <label><?php echo trans('lang.asset');?></label>
                            <select name="asset" type="text" id="asset" class=" form-control" >
                                <option  cat="" value=""><?php echo trans('lang.asset');?></option>
                            </select>
                        </div>
                        <div class="component-sec form-group d-none">
                            <label><?php echo trans('lang.component');?></label>
                            <select name="component" type="text" id="component" class=" form-control" >
                                <option cat="" value=""><?php echo trans('lang.component');?></option>
                            </select>
                        </div>
                        <div class="form-group">
                        <label for="period" class="control-label"><?php echo trans('lang.period');?></label> 
								<div class="input-group mb-0" >                                    
									<input class="form-control number" required="" placeholder="<?php echo trans('lang.period');?>" id="period" name="period" type="text">
                                    <span class="input-group-addon border-1" id="period" ><?php echo trans('lang.month');?></span>
                                </div>
                                <label class="error" for="period"></label>
                        </div>
                        
                        <div class="form-group">
                            <label for="assetvalue" class="control-label"><?php echo trans('lang.assetvalue');?></label> 
								<div class="input-group mb-0">
									<span class="input-group-addon setcurrency border-1" id="currency"></span>                                      
									<input class="form-control number" required="" placeholder="<?php echo trans('lang.assetvalue');?>" id="assetvalue" name="assetvalue" type="text">
								</div>
                                <label class="error assetvalue" for="assetvalue"></label>
                           
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"
                            id="save"><?php echo trans('lang.save');?></button>
                        <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo trans('lang.close');?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end add data-->

    <!--edit new data -->
    <div id="edit" class="modal fade" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" id="formedit">
                    <div class="modal-header">
                       
                        <h5 class="modal-title"><?php echo trans('lang.edit_data');?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="messagehigher display-none alert alert-success"><?php echo trans('lang.assetvaluecannothigh');?></div>
                        
                        <div class="asset-sec form-group d-none">
                            <label><?php echo trans('lang.asset');?></label>
                            <input type="text" id="editasset" name="editasset" class="form-control" readonly/>
                            
                        </div>
                        <div class="component-sec form-group d-none">
                            <label><?php echo trans('lang.component');?></label>
                            <input type="text" id="editcomponent" name="editcomponent" class="form-control" readonly/>
                            
                        </div>
                        <div class="form-group">
                        <label for="editperiod" class="control-label"><?php echo trans('lang.period');?></label> 
								<div class="input-group mb-0" >                                    
									<input class="form-control number" required="" placeholder="<?php echo trans('lang.period');?>" id="editperiod" name="editperiod" type="text">
                                    <span class="input-group-addon border-1" id="period" ><?php echo trans('lang.month');?></span>
                                </div>
                                <label class="error" for="editperiod"></label>
                        </div>
                        
                        <div class="form-group">
                            <label for="editassetvalue" class="control-label"><?php echo trans('lang.assetvalue');?></label> 
								<div class="input-group mb-0">
									<span class="input-group-addon setcurrency border-1" id="currency"></span>                                      
									<input class="form-control number" required="" placeholder="<?php echo trans('lang.assetvalue');?>" id="editassetvalue" name="editassetvalue" type="text">
								</div>
                                <label class="error" for="editassetvalue"></label>
                           
                        </div>
</div>
                    <div class="modal-footer">
                        <input type="hidden" name="editcostcomponent" id="editcostcomponent"/>
                        <input type="hidden" name="editcostasset" id="editcostasset"/>
                        <input type="hidden" name="id" id="editid"/>
                        <button type="submit" class="btn btn-primary"
                            id="saveedit"><?php echo trans('lang.save');?></button>
                        <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo trans('lang.close');?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end edit data-->

    <!--delete data -->
    <div class="modal fade" id="delete" role="dialog">
        <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="#" id="formdelete">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo trans('lang.delete');?></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p><?php echo trans('lang.delete_confirm');?></p>
                    <input type="hidden" value="" name="id" id="iddelete"/>
            
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="delete"><?php echo trans('lang.delete');?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('lang.close');?></button>
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

    //get asset list
    $.ajax({
        type: "GET",
		url: "{{ url('assetnotbyid')}}",
		dataType: "JSON",
		success: function(html) {
            var objs = html.message;
            jQuery.each(objs, function (index, record) {
                var id = decodeURIComponent(record.id);
                var name = decodeURIComponent(record.name);
                var cost = decodeURIComponent(record.cost);
                $("#asset").append($("<option></option>")
                    .attr("value",id)
                    .attr("cat",cost)
                    .text(name)); 
                
            });
		}   
    }); 

    //get component list
    $.ajax({
        type: "GET",
		url: "{{ url('componentnotbyid')}}",
		dataType: "JSON",
		success: function(html) {
            var objs = html.message;
            jQuery.each(objs, function (index, record) {
                var id = decodeURIComponent(record.id);
                var name = decodeURIComponent(record.name);
                var cost = decodeURIComponent(record.cost);
                $("#component").append($("<option></option>")
                    .attr("value",id)
                    .attr("cat",cost)
                    .text(name)); 
                
            });
		}   
    }); 


    

    $('#data').DataTable({
        ajax: "{{ url('depreciation')}}",
        columns: [{
            data: 'id',
                orderable: false,
                searchable: false,
                visible: false
            },
           
            {
                data: 'name'
            },
            {
                data: 'cost'
            },
            {
                data: 'period'
            },
            {
                data: 'category'
            },
            {
                data: 'assetvalue'
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
                title: '<?php echo trans('lang.depreciation_list');?>',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'csv',
                text: 'CSV <i class="fa fa-file-excel-o"></i>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: '<?php echo trans('lang.depreciation_list');?>',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                text: 'PDF <i class="fa fa-file-pdf-o"></i>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: '<?php echo trans('lang.depreciation_list');?>',
                orientation: 'landscape',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                },
                customize: function(doc) {
                    doc.styles.tableHeader.alignment = 'left';
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
                        .join('*').split('');
                }
            },
            {
                extend: 'print',
                title: '<?php echo trans('lang.depreciation_list');?>',
                className: 'btn btn-sm btn-fill btn-info ',
                text: 'Print <i class="fa fa-print"></i>',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            }
        ]
    });


//asset value < cost
var stopsubmit = false;
$('#assetvalue').on('change', function(e) {
            var currentcost;
            var category = $('#category option:selected').val();
            if(category=='1'){
                var currentcost = parseInt($('#asset option:selected').attr('cat'));
            }else{
                currentcost = parseInt($('#component option:selected').attr('cat'));
            }
            var inputvalue = parseInt($("#assetvalue").val());
            if(inputvalue > currentcost){
                $("#add .messagehigher").css("display","block");
                stopsubmit = true;
            }else{
                $("#add .messagehigher").css("display","none");
                stopsubmit = false;
            }
        });




//add data
$("#formadd").validate({
    submitHandler: function(form, event) {
        if(stopsubmit == true){
            event.preventDefault();
        }
        else{
            $.ajax({
                method: "POST",
                url: "{{ url('savedepreciation')}}",
                data: $("#formadd").serialize(),
                dataType: "JSON",
                success: function(data) {
                    $("#messagesuccess").css({'display':"block"});
                    $('#add').modal('hide');
                    window.setTimeout(function(){location.reload()},2000)
                }
            });
        }
    }
});

//asset value < cost
var stopsubmitedit = false;
$('#editassetvalue').on('change', function(e) {
            var currentcost;
            if ($("#edit .component-sec").hasClass('d-none')){
                currentcost = parseInt($('#editcostasset').val());
            } 
            else if ($("#edit .asset-sec").hasClass('d-none')){
                currentcost = parseInt($('#editcostcomponent').val());
            }
            
            var inputvalue = parseInt($("#editassetvalue").val());
            if(inputvalue > currentcost){
                $("#edit .messagehigher").css("display","block");
                stopsubmitedit = true;
            }else{
                $("#edit .messagehigher").css("display","none");
                stopsubmitedit = false;
            }
});

//edit data
$("#formedit").validate({
    submitHandler: function(form, event) {
        if(stopsubmitedit == true){
            event.preventDefault();
        }
        else{
        $.ajax({
			method: "POST",
            url: "{{ url('updatedepreciation')}}",
            data: $("#formedit").serialize(),
            dataType: "JSON",
            success: function(data) {
				$("#messageupdate").css({'display':"block"});
				$('#edit').modal('hide');
				window.setTimeout(function(){location.reload()},2000)
                }
		    });
        }
    }
});

//delete data
$("#formdelete").validate({
    submitHandler: function(form) {
        $.ajax({
			method: "POST",
            url: "{{ url('deletedepreciation')}}",
            data: $("#formdelete").serialize(),
            dataType: "JSON",
            success: function(data) {
				$("#messagedelete").css({'display':"block"});
				$('#delete').modal('hide');
				window.setTimeout(function(){location.reload()},2000)
            }
		});
    }
});

//show edit data
$('#edit').on('show.bs.modal', function(e) {
    var $modal = $(this),
    id = $(e.relatedTarget).attr('customdata');
	$.ajax({
		type: "POST",
		url: "{{ url('depreciationbyid')}}",
		data: {id:id},
		dataType: "JSON",
		success: function(data) {
			$("#editid").val(id);
            $("#editasset").val(data.message.asset);
            $("#editcomponent").val(data.message.component);
            $("#editperiod").val(data.message.period);
			$("#editassetvalue").val(data.message.assetvalue);
            $("#editcostcomponent").val(data.message.componentcost);
            $("#editcostasset").val(data.message.assetcost);
            var componentid = data.message.componentid;
            var assetid     = data.message.assetid;
            
            if(assetid > 0){
                $(".asset-sec").removeClass('d-none');
                $(".component-sec").addClass('d-none');
                $("#asset").addClass('required');
            }
            if(componentid > 0){
                $(".asset-sec").addClass('d-none');
                $(".component-sec").removeClass('d-none');
                $("#component").addClass('required');
            } 
		}   
	});
    
});
//change asset type
$('#component, #asset').on('change', function(e) {
    $('#assetvalue').val('');
});

// change category
$('#category').on('change', function(e) {
    var category = $('#category').val();
    $('#assetvalue').val('');
    if(category=='1'){
        $(".asset-sec").removeClass('d-none');
        $(".component-sec").addClass('d-none');
        $("#asset").addClass('required');
        $('#component').val('');
    }
    else if(category=='2'){
        $(".asset-sec").addClass('d-none');
        $(".component-sec").removeClass('d-none');
        $("#component").addClass('required');
        $('#asset').val('');
    } else{
        $(".asset-sec").addClass('d-none');
        $(".component-sec").addClass('d-none');
    }
});

//show delete data
$('#delete').on('show.bs.modal', function(e) {
    var $modal = $(this),
    id = $(e.relatedTarget).attr('customdata');
    console.log(id);
    $("#iddelete").val(id);
});



})(jQuery);
</script>
@endsection