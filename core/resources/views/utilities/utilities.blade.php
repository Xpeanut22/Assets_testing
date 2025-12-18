@extends('main')
@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12 col-md-11">
				<div class="card">
					<div class="header">
						<h4 class="title"><?php echo trans('lang.all_utilities'); ?></h4>
						<hr>
					</div>
					<div class="content">
						<div class="row">
							<div class="col-lg-6" style="border-right: 1px solid #f0f0f0;">
								@if(Auth::check())

								<p class="text-primary"><i class="ti-angle-right"></i><a href="{{ URL::to( 'assettypelist') }}"><?php echo trans('lang.assettype'); ?></a> </p>
								<hr>

								@endif
								@if(Auth::check())

								<p class="text-primary"><i class="ti-angle-right"></i><a href="{{ URL::to( 'brandlist') }}"><?php echo trans('lang.brand'); ?></a> </p>
								<hr>

								@endif
								@if(Auth::check())

								<p class="text-primary"><i class="ti-angle-right"></i><a href="{{ URL::to( 'supplierlist') }}"><?php echo trans('lang.supplier'); ?></a> </p>
								<hr>

								@endif
								@if(Auth::check())

								<p class="text-primary"><i class="ti-angle-right"></i><a href="{{ URL::to( 'receiverlist') }}"><?php echo trans('lang.receiver_list'); ?></a> </p>
								<hr>

								@endif
								@if(Auth::check())

								<p class="text-primary"><i class="ti-angle-right"></i><a href="{{ URL::to( 'usedlist') }}">Use of Equipment List</a> </p>
								<hr>

								@endif


							</div>
							<div class="col-lg-6">
								@if(Auth::check())

								<p><i class="ti-angle-right"></i><a href="{{ URL::to( 'locationlist') }}"><?php echo trans('lang.location'); ?></a> </p>
								<hr>

								@endif
								@if(Auth::check())

								<p><i class="ti-angle-right"></i><a href="{{ URL::to( 'employeeslist') }}"><?php echo trans('lang.clients'); ?></a> </p>
								<hr>

								@endif
								@if(Auth::check())

								<p><i class="ti-angle-right"></i><a href="{{ URL::to( 'departmentlist') }}"><?php echo trans('lang.department'); ?></a> </p>
								<hr>

								@endif
								@if(Auth::check())

								<p class="text-primary"><i class="ti-angle-right"></i><a href="{{ URL::to( 'unitlist') }}">Unit List</a> </p>
								<hr>

								@endif
								@if(Auth::check())

								<p class="text-primary"><i class="ti-angle-right"></i><a href="{{ URL::to( 'categorylist') }}">Category List</a> </p>
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