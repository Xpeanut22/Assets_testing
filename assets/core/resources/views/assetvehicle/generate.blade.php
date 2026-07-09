
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Generate Label</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{ asset('js/general.js')}}"></script>
</head>

<style type="text/css">
	table{
		font-family: Arial, Serif;
	}

	p{
		margin: 0;
	}
	.assettag, .assetserial, .assettype2{
		font-size: 12px;
	}
	.assetname{
		font-size: 16px;
		font-weight: bold;
		margin-bottom: 5px;
	}
	.padding-right{
		padding-right: 10px;
	}
</style>
<body>


<table width="200" align="center" border="1" cellspacing="0" cellpadding="0">
<input type="hidden" value="{{ $id }}" name="id" id="id" />

<tr>
	<td align="left" class="padding-right" >
     
     <p class="assetname"></p> 
     <p class="assettag"></p>      
     <p class="assetserial"></p>
     <p class="assettype2"></p>                                 
	</td>
	<td width="70" align="right">
	<div class="assetbarcode"></div>       
	</td>
</tr>



</table>

</table>

<script>
	(function($) {
		"use strict";
		var id = $("#id").val();
    	$.ajax({
	        type: "POST",
	        url: "{{ url('assetbyid')}}",
	        data: {
	            id: id
	        },
	        dataType: "JSON",
	        success: function(data) {
	            $(".assetname").html(data.message.assetname);
	            $(".assettag").html(data.message.assettag);
	            $(".assettype").html(data.message.type);
	            $(".assetsupplier").html(data.message.supplier);
	            $(".assetstatus").html(data.assetstatus);
	            $(".assetbrand").html(data.message.brand);
	            $(".assettype2").html(data.message.type);
	            $(".assetpurchasedate").html(data.assetpurchasedate);
	            $(".assetcost").html(data.assetcost);
	            $(".assetwarranty").html(data.assetwarranty);
	            $(".assetdescription").html(data.message.description);
	            $(".assetcreated").html(data.assetcreated_at);
	            $(".assetupdated").html(data.assetupdated_at);
	            $(".assetserial").html(data.message.serial);
	            $(".assetlocation").html(data.message.location);
	            $(".assetbarcode").html(data.assetbarcode);
	        }
	    });
	})(jQuery);

</script>
</body>
</html>