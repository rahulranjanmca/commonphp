<?php
namespace Canigenus\CommonPhp\Controllers;

use Canigenus\CommonPhp\Controllers\LaravelMultiTenantBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Canigenus\CommonPhp\Services\ApplicationPropertiesService;
class ApplicationPropertiesMultiTenantController extends LaravelMultiTenantBaseController
{
	public function __construct(ApplicationPropertiesService $serviceInterface)
	{
		parent::__construct($serviceInterface,'admin.mproperties_edit','admin.mproperties_list', "Application Properties",  [
				'key' => 'required|max:255',
				'value' => 'required'
		], 'applicationproperties');
	}
	
	
	public function update($clientId, $id, Request $request)
	{
		
	$view=parent::update($request, $id);
	Cache::forever($view->getData()['item']->key, $view->getData()['item']->value);
	return $view;
	}
}