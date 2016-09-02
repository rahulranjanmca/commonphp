<?php

namespace Canigenus\CommonPhp\Controllers;

use Canigenus\CommonPhp\Controllers\LaravelMultiTenantBaseController;
use Canigenus\CommonPhp\Services\UserServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Canigenus\CommonPhp\Services\WebsiteSettingService;

class WebsiteSettingController extends LaravelMultiTenantBaseController {
	public function __construct(WebsiteSettingService $websiteSettingService, UserServiceInterface $userService) {
		parent::__construct ( $websiteSettingService, $userService, 'admin/website_setting_edit', 'admin/website_list', 'Website Setting', [ 
				'name' => 'required',
				'logo'=>'file'
		], "website_setting" );
	}
	public function processRequestBeforeSaveOrUpdate(Request $request) {
		$request->merge ( [ 
				'user_id' => $request->session ()->get ( "userDetails" ) ['id'] 
		] );
	}
	public function processResponseBeforeView(Model $response) {
		// $response->setAttribute('payment_processors', json_decode($response->getAttribute('payment_processors')));
	}
	public function edit($clientId, $id, Request $request) {
		$user = $request->session ()->get ( "userDetails" );
		$item = $this->service->getList ( [ 
				'clientId' => $clientId 
		], 1 ) [0];
		if ($this->isAuthorized ( $clientId, 'view', $request, $user, $item )) {
			$this->processResponseBeforeView ( $item );
			$pageVariables = $this->editPageLoad ( $request );
			return view ( $this->editViewName, compact ( 'item', 'clientId', 'pageVariables' ) );
		} else {
			return response ( null, 401 );
		}
	}
	
	public function update($clientId,$id, Request $request)
	{
	
		$valid=  Validator::make($request->all(),$this->validations);
		if($valid->fails())
		{
			return redirect()
			->back()
			->withInput($request->all())
			->withErrors($valid->errors());
		}
		    $file= $request->file('logo');
		    if($file!=null)
		    {
		    $extension = $file->getClientOriginalExtension();
		    $path= 'public/'.$clientId.'/logo/'.$file->getFilename().'.'.$extension;
		    Storage::disk('local')->put($path,  File::get($file));
		    $request->merge(['logo_path'=>$path]);
		    }
			$user =$request->session()->get("userDetails");
			$data =$this->service->get($id);
			if($this->isAuthorized($clientId,'update',$request,  $user,$data ))
			{
				$this->processRequestBeforeSaveOrUpdate($request);
				$request->merge(['clientId'=>$clientId]);
				$item=$this->service->update($id, $request->all());
				Cache::forever('website-setting',$item);
				$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
				$this->processResponseBeforeView($item);
				$pageVariables=$this->editPageLoad($request);
				return view($this->editViewName,compact('clientId','item','pageVariables'));
			}
			else{
				return response(null, 401);
			}
		
	}
}