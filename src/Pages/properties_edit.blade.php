@extends('admin.template') @section('css') textarea.note-codable {
display: none; } @stop @section('javascript')

<!--   <script data-main="src/js/app" data-editor-type="bs3" src="//cdnjs.cloudflare.com/ajax/libs/require.js/2.1.9/require.min.js"></script> -->
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>
  tinymce.init({
	  selector: '.description',
	  height: 500,
	  theme: 'modern',
	  plugins: [
	    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
	    'searchreplace wordcount visualblocks visualchars code fullscreen',
	    'insertdatetime media nonbreaking save table contextmenu directionality',
	    'emoticons template paste textcolor colorpicker textpattern imagetools'
	  ],
	  toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
	  toolbar2: 'print preview media | forecolor backcolor emoticons',
	  image_advtab: true,
	  templates: [
	    { title: 'Test template 1', content: 'Test 1' },
	    { title: 'Test template 2', content: 'Test 2' }
	  ],
	  content_css: [
	    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
	    '//www.tinymce.com/css/codepen.min.css'
	  ]
	 });</script>

@stop @section('content')

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
			Form::model($item,array('route'=>array('applicationproperties.update',$item->id),
			'class'=>'form-horizontal')) }} <input type="hidden" name="_method"
				value="PUT"> @else {{
			Form::open(array('route'=>array('applicationproperties.store'), 'method' =>
			'post', 'class'=>'form-horizontal')) }} @endif

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						<strong>Role</strong> Create
					</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<p>Role Create.</p>
				</div>
				<div class="panel-body">

					<div class="row">

						<div class="col-md-6">

							<div class="form-group">
								<label class="col-md-3 control-label">Key</label>
								<div class="col-md-9">
									<div class="input-group">
										<span class="input-group-addon"><span class="fa fa-pencil"></span></span>
										{{ Form::text('key',null,['class' => 'form-control',
										'placeholder' => 'Key'])}}
									</div>
									<span class="help-block">Key</span>
								</div>
							</div>

							




						</div>

						<div class="col-md-6">



							

						</div>
					</div>


					
						<div class="row">
							<label class="col-md-12">Value</label>
							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon"><span class="fa fa-pencil"></span></span>
									{{ Form::textarea('value',null,['class' => 'form-control',
									'placeholder' => 'Value'])}}
								</div>
								<span class="help-block">Value</span>
							</div>
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
