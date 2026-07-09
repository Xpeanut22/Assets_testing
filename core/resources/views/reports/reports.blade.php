@extends('main')
@section('content')
<div class="content">
    <div class="container-fluid">
		<div class="row">
			<div class="col-lg-12 col-md-11">
				 <div class="card">
					<div class="header">
						<h4 class="title"><?php echo trans('lang.all_reports');?></h4>
						<hr>
					</div>
					<div class="content">
						<div class="row">
							<div class="col-lg-6" style="border-right: 1px solid #f0f0f0;">
								@if(Auth::check())
									
								<p class="text-primary"><i class="ti-angle-right"></i><a href="{{ URL::to( 'reports/assetactivity') }}"><?php echo trans('lang.assetactivity');?></a> </p>
								<hr>
									
								@endif	
								@if(Auth::check())
									
								<p class="text-primary"><i class="ti-angle-right"></i><a href="{{ URL::to( 'reports/componentactivity') }}"><?php echo trans('lang.issueactivity');?></a> </p>
								<hr>
									
								@endif		
								@if(Auth::check())
								
								<p class="text-primary"><i class="ti-angle-right"></i><a href="{{ URL::to( 'reports/maintenance') }}"><?php echo trans('lang.maintenancereport');?></a> </p>
								<hr>
									
								@endif
								@if(Auth::check())
									
								<p><i class="ti-angle-right"></i><a href="{{ URL::to( 'reports/bytype') }}"><?php echo trans('lang.reportbytype');?></a> </p>
								<hr>
									
								@endif		
							</div>
							<div class="col-lg-6">
								@if(Auth::check())
									
								<p><i class="ti-angle-right"></i><a href="{{ URL::to( 'reports/bystatus') }}"><?php echo trans('lang.reportbystatus');?></a> </p>
								<hr>
									
								@endif
								@if(Auth::check())
									
								<p><i class="ti-angle-right"></i><a href="{{ URL::to( 'reports/bysupplier') }}"><?php echo trans('lang.reportbysupplier');?></a> </p>
								<hr>
									
								@endif
								@if(Auth::check())
									
								<p><i class="ti-angle-right"></i><a href="{{ URL::to( 'reports/bylocation') }}"><?php echo trans('lang.reportbylocation');?></a> </p>
								<hr>
									
								@endif
								
							</div>
						</div>
						
					</div>
				 </div>
			</div> 
		</div>

    </div>
</div>	


@endsection