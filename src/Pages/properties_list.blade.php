@extends('admin.template') @section('content')
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
	<li><a href="#">Home</a></li>
	<li><a class="active">Website Settings</a></li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">

	<div class="row">
		<div class="col-md-12">

			<!-- START DEFAULT DATATABLE -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Default</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span
								class="fa fa-angle-down"></span></a></li>
						<li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
						<li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="flash-message">
						@foreach (['danger', 'warning', 'success', 'info'] as $msg)
						@if(Session::has('alert-' . $msg))

						<p class="alert alert-{{ $msg }}">
							{{ Session::get('alert-' . $msg) }} <a href="#" class="close"
								data-dismiss="alert" aria-label="close">&times;</a>
						</p>
						@endif @endforeach
					</div>


					<table class="table datatable">
						<thead>
							<tr>
								<th>Key</th>
								<th>Value</th>
								
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($items as $item)
							<td>{{ $item->key }}</td>
							<td>{{ $item->value }}</td>
							<td><a href="{{ route('applicationproperties.edit',array(  $item->id)) }}"
								class="btn btn-info">View</a> 
								 {{
									Form::open(array('route'=>array('applicationproperties.destroy',   $item->id), 
									'class'=>'form-horizontal')) 
								 }}
								<button
								type="submit"
								class="btn btn-danger">Delete</a>
								{!! Form::close() !!}
								<input type="hidden" name="_method" value="delete">
								</td>
							<tr>
								<!--     <td>Tiger Nixon</td>
                                                <td>System Architect</td>
                                                <td>Edinburgh</td>
                                                <td>61</td>
                                                <td>2011/04/25</td>
                                                <td>$320,800</td> -->
							</tr>
							@endforeach


						</tbody>
					</table>
					{{ $items->links() }}


				</div>
			</div>
			<!-- END DEFAULT DATATABLE -->

		</div>
	</div>


	<!-- END PAGE CONTENT WRAPPER -->
</div>
@stop
