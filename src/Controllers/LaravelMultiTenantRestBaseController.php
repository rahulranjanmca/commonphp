<?php
namespace Canigenus\CommonPhp\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Canigenus\CommonPhp\Services\ServiceInterface;

class LaravelMultiTenantRestBaseController  extends Controller {
	
	
	
	protected $service;
	protected $userService;
	protected $canBeUpdatedOnlyByOwner=false;
	protected $showOnlyYourOwnData=false;
	protected $isLoginRequired=true;
	protected $needRoleAuthentication=true;	
	protected $modelName;
	
	public function __construct(ServiceInterface $serviceInterface, ServiceInterface $userServiceInterface, $validations, $modelName)
	{
		$this->service = $serviceInterface;
		$this->userService=$userServiceInterface;
		$this->validations=$validations;
		$this->modelName=$modelName;
	}
	
	public function isAuthorized($clientId, $function, Request $request,  $user=null, $data=null){
		if($user==null)
		{
		$user =$this->userService->get(Authorizer::getResourceOwnerId());
		}
		if(!$user->getAttribute('admin') && $clientId!=$user->getAttribute('client_id'))
		{
			return false;
		}
		if((!$user->getAttribute('admin') && $data!=null) && ($clientId!=$user->getAttribute('client_id') || $data->getAttribute('client_id')!=$user->getAttribute('client_id')))
		{
			return false;
		}
		if($this->needRoleAuthentication && !$user->getAttribute('admin') && $clientId!=$user->getAttribute('client_id')){
			if(!in_array($this->modelName.'_'.$function,$this->userService->getPermissionsByUserId(Authorizer::getResourceOwnerId())))
			{
				return false;
			}
		}
	
		if($this->canBeUpdatedOnlyByOwner && $data!=null && $data->getAttribute('user_id')!=$user->getAttribute('id'))
		{
			return false;
		}
		if(!$this->isAuthrizedExtra($clientId, $function, $request,  $user=null, $data=null)){
			return false;
		}
		return true;
	}
	protected function isAuthrizedExtra($clientId, $function, Request $request,  $user=null, $data=null)
	{
		return true;
	}
	
		
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($clientId, Request $request)
	{ 
		if($this->isLoginRequired)
		{
		$user =$this->userService->get(Authorizer::getResourceOwnerId());
		if($this->isAuthorized($clientId,'view',$request,$user))
		{
			if(!$user->getAttribute('admin'))
			{
				$request->merge(['user_id'=>$clientId]);
			}
			if(!$user->getAttribute('admin') && !$user->getAttribute('client_admin') && $this->showOnlyYourOwnData){
				$request->merge(['user_id'=>Authorizer::getResourceOwnerId()]);
			}
			return response($this->service->getList($request));
		}
		else{
			return response(null, 401);
		}
		}
		else{
			return response($this->service->getList($request));
		}
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store($clientId, Request $request)
	{
		if($this->isLoginRequired)
		{
		$valid=  Validator::make($request->all(),$this->validations);
		if($valid->fails())
		{
			return response([
					'message' => 'validation_faild',
					'errors' => $valid->errors()
			],400);
		}
		$user =$this->userService->get(Authorizer::getResourceOwnerId());
		if($this->isAuthorized($clientId,'create',$request,$user))
		{
			$request->merge(['client_id'=>$clientId, 'created_by'=>Authorizer::getResourceOwnerId(), 'updated_by'=>Authorizer::getResourceOwnerId()]);
			return response($this->service->save($request->all()),200);
		}
		else{
		return response(null, 401);
		}
		}
		else{
		return response($this->service->save($request->all()),200);
		}
		
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($clientId, $id, Request $request)
	{
		if($this->isLoginRequired)
		{
		$user =$this->userService->get(Authorizer::getResourceOwnerId());
		$response=$this->service->get($id);
		if($this->isAuthorized($clientId, 'view',$request, $user,$response)){
	
		if($this->showOnlyYourOwnData && !$user->getAttribute('admin') && !$user->getAttribute('client_admin')){
				return response($response); 
		}
		else if($this->showOnlyYourOwnData && $response->getAttribute('user_id')==$user->getAttribute('id')){
			return response($response);
		}
		else if(!$this->showOnlyYourOwnData)
		{
			return response($response);
		}
		else{
			return response(null,401);
		}
		}
		else{
			return response(null,401);
		}
		}
		else{
			return response($response);
		}
	}
	
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update($clientId,  $id, Request $request)
	{
		$valid=  Validator::make($request->all(),$this->validations);
		if($valid->fails())
		{
			return response([
					'message' => 'validation_faild',
					'errors' => $valid->errors()
			],400);
		}
		if($this->isLoginRequired)
		{
		$user =$this->userService->get(Authorizer::getResourceOwnerId());
		$data =$this->service->get($id);
		if($this->isAuthorized($clientId, 'update',$request,  $user,$data ))
		{
			$request->merge(['client_id'=>$clientId, 'updated_by'=>Authorizer::getResourceOwnerId()]);
			$response=$this->service->update($id,$request->all());
			return response($response);
		}
		else{
			return response(null, 401);
		}
		}
		else{
			$request->merge(['client_id'=>$clientId, 'updated_by'=>Authorizer::getResourceOwnerId()]);
			$response=$this->service->update($id,$request->all());
			return response($response);
		}
		
		
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($clientId, $id,  Request $request)
	{
		if($this->isLoginRequired)
		{
		$user =$this->userService->get(Authorizer::getResourceOwnerId());
		$data=$this->service->get($id);
		
		if($this->isAuthorized($clientId, 'delete', $request,  $user,$data ))
		{
			$this->service->delete($id);
			return response(null,200);
		}
		else{
			return response(null, 401);
		}
		}
		else{
			$this->service->delete($id);
			return response(null,200);
		}
	}
	
	protected $validations;
	
	
}