<?php
namespace Canigenus\CommonPhp\Controllers;

use Canigenus\CommonPhp\Services\ApplicationPropertiesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
class ApplicationPropertiesController extends LaravelBaseController
{
	public function __construct(ApplicationPropertiesService $serviceInterface)
	{
		parent::__construct($serviceInterface,'admin.properties_edit','admin.properties_list', "Application Properties",  [
				'key' => 'required|max:255',
				'value' => 'required'
		], 'applicationproperties');
	}
	
	
	public function update($id, Request $request)
	{
		
	$view=parent::update($request, $id);
	Cache::forever($clientId.'-'.$view->getData()['item']->key, $view->getData()['item']->value);
	return $view;
	}
}