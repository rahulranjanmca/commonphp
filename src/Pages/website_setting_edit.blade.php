@extends('admin.template')
@section('content')
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
			<div class="flash-message">
				@foreach (['danger', 'warning', 'success', 'info'] as $msg)
				@if(Session::has('alert-' . $msg))

				<p class="alert alert-{{ $msg }}">
					{{ Session::get('alert-' . $msg) }} <a href="#" class="close"
						data-dismiss="alert" aria-label="close">&times;</a>
				</p>
				@endif @endforeach
			</div>
			<!-- end .flash-message -->
			@if (count($errors) > 0)
			<div class="flash-message">
				<p class="alert alert-danger">
					@foreach ($errors->all() as $error) {{ $error }} @endforeach <a
						href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				</p>
			</div>

			@endif @if(isset($item)) {{
			Form::model($item,array('route'=>array('{clientId}.website-settings.update',$clientId,$item->id),
			'method' => 'put', 'class'=>'form-horizontal', 'enctype'=>"multipart/form-data")) }} @else {{
			Form::open(array('route'=>array('{clientId}.email-lists.store', $clientId), 'method' => 'post',
			'class'=>'form-horizontal')) }} @endif

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						<strong>Email List</strong> Create
					</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<p>Email List Create.</p>
				</div>
				<div class="panel-body">

					<div class="row">

						<div class="col-md-6">

							<div class="form-group">
								<label class="col-md-3 control-label">Name</label>
								<div class="col-md-9">
									<div class="input-group">
										<span class="input-group-addon"><span class="fa fa-pencil"></span></span>
										{{ Form::text('name',null,['class' => 'form-control',
										'placeholder' => 'Email List Name'])}}
									</div>
									<span class="help-block">Email List Name</span>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-3 control-label">Name</label>
								<div class="col-md-9">
									<div class="input-group">
										<span class="input-group-addon"><span class="fa fa-pencil"></span></span>
										{{ Form::file('logo',null,['class' => 'form-control',
										'placeholder' => 'Email List Name'])}}
									</div>
									<span class="help-block">Email List Name</span>
								</div>
							</div>

							


						</div>

						<div class="col-md-6">
							

						</div>
						<div class="panel-footer">
							<button class="btn btn-default">Clear Form</button>
							<button class="btn btn-primary pull-right">Submit</button>
						</div>
					</div>
					{!! Form::close() !!}

				</div>
			</div>

		</div>
		<!-- END PAGE CONTENT WRAPPER -->
	</div>
@stop