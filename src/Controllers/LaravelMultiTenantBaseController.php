<?php
namespace Canigenus\CommonPhp\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Canigenus\CommonPhp\Services\ServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

abstract class LaravelMultiTenantBaseController  extends Controller {
	
	
	protected $service;
	protected $userService;
	protected $canBeUpdatedOnlyByOwner=false;
	protected $showOnlyYourOwnData=false;
	protected $isLoginRequired=true;
	protected $needRoleAuthentication=true;	
	protected $modelName;
	protected $permissionModelName;
	
	public function __construct(ServiceInterface $serviceInterface,\App\Http\Services\ServiceInterface $userService, $editViewName,$searchViewName, $modelName, $validations, $permissionModelName)
	{
		$this->service = $serviceInterface;
		$this-> editViewName=$editViewName;
		$this-> searchViewName=$searchViewName;
		$this-> modelName=$modelName;
		$this-> permissionModelName=$permissionModelName;
		$this-> validations=$validations;
		$this->service = $serviceInterface;
		$this->userService = $userService;
	}
	
	
	public function isAuthorized($clientId, $function, Request $request, $user, $data=null){
		if(!$user->getAttribute('admin') && $clientId!=$user->getAttribute('client_id'))
		{
			return false;
		}
		if((!$user->getAttribute('admin') && $data!=null) && ($clientId!=$user->getAttribute('client_id') || $data->getAttribute('client_id')!=$user->getAttribute('client_id')))
		{
			return false;
		}
		if($this->needRoleAuthentication && !$user->getAttribute('admin') && $clientId!=$user->getAttribute('client_id')){
			if(!in_array($this->modelName.'_'.$function,$request->session()->get("permissions")))
			{
				return false;
			}
		}
	
		if($this->canBeUpdatedOnlyByOwner && $data!=null && $data->getAttribute('user_id')!=$user->getAttribute('id'))
		{
			return false;
		}
		if(!$this->isAuthrizedExtra($clientId, $function, $request,  $user, $data=null)){
			return false;
		}
		return true;
	}
	
	protected function isAuthrizedExtra($clientId, $function, Request $request,  $user, $data=null)
	{
		return true;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function search($clientId, Request $request)
	{
		$searchCriteria=[];
		return view($this->searchViewName,compact('clientId','searchCriteria'));
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
			$user =$this->$request->session()->get("userDetails");
			if($this->isAuthorized($clientId,'view',$request,$user))
			{
				if(!$user->getAttribute('admin'))
				{
					$request->merge(['user_id'=>$clientId]);
				}
				if(!$user->getAttribute('admin') && !$user->getAttribute('client_admin') && $this->showOnlyYourOwnData){
					$request->merge(['user_id'=>$user->getAttribute("id")]);
				}
				$items->appends($request->except(array('page','clientId')));
				$searchCriteria=$request->except(array('page','clientId'));
				return view($this->searchViewName,compact('items','clientId','searchCriteria'));
			}
			else{
				return response(null, 401);
			}
		}
		else{
				$items->appends($request->except(array('page','clientId')));
				$searchCriteria=$request->except(array('page','clientId'));
				return view($this->searchViewName,compact('items','clientId','searchCriteria'));
		}
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($clientId)
	{
		return view($this->editViewName,compact('clientId'));
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
			return redirect()
			->back()
			->withInput($request->all())
			->withErrors($valid->errors());
		}
		$user =$request->session()->get("userDetails");
		if($this->isAuthorized($clientId,'create',$request,$user))
		{
			$this->processRequestBeforeSaveOrUpdate($request);
			$request->merge(['client_id'=>$clientId, 'created_by'=>$user->getAttribute("id"), 'updated_by'=>$user->getAttribute("id")]);
			$item=$this->service->save($request->all());
			$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
			$pageVariables=$this->editPageLoad($request);
			return view($this->editViewName, compact("clientId","pageVariables","item"));
		}
		else{
		return response(null, 401);
		}
		}
		else{
		$this->processRequestBeforeSaveOrUpdate($request);
		$request->merge(['clientId'=>$clientId]);
		$item=$this->service->save($request->all());
		$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
		$pageVariables=$this->editPageLoad($request);
		return view($this->editViewName, compact("clientId","pageVariables","item"));
		}
	}
	
	public function processRequestBeforeSaveOrUpdate(Request $request){
	}
	public function processResponseBeforeView(Model $response){
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($clientId, $id)
	{
		if($this->isLoginRequired)
		{
		$user =$request->session()->get("userDetails");
		$response=$this->service->get($id);
		if($this->isAuthorized($clientId, 'view',$request, $user,$response)){
	
		if($this->showOnlyYourOwnData && !$user->getAttribute('admin') && !$user->getAttribute('client_admin')){
				$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		return view(isset($this->viewViewName)?$this->viewViewName:$this->editViewName,compact('item','clientId'));
		}
		else if($this->showOnlyYourOwnData && $response->getAttribute('user_id')==$user->getAttribute('id')){
			$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		return view(isset($this->viewViewName)?$this->viewViewName:$this->editViewName,compact('item','clientId'));
		}
		else if(!$this->showOnlyYourOwnData)
		{
			$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		return view(isset($this->viewViewName)?$this->viewViewName:$this->editViewName,compact('item','clientId'));
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
		$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		return view(isset($this->viewViewName)?$this->viewViewName:$this->editViewName,compact('item','clientId'));
		}
	
		
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($clientId, $id)
	{
		if($this->isLoginRequired)
		{
		$user =$request->session()->get("userDetails");
		$response=$this->service->get($id);
		if($this->isAuthorized($clientId, 'view',$request, $user,$response)){
	
		if($this->showOnlyYourOwnData && !$user->getAttribute('admin') && !$user->getAttribute('client_admin')){
				$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		$pageVariables=$this->editPageLoad($request);
		return view($this->editViewName,compact('item','clientId','pageVariables'));
		}
		else if($this->showOnlyYourOwnData && $response->getAttribute('user_id')==$user->getAttribute('id')){
			$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		$pageVariables=$this->editPageLoad($request);
		return view($this->editViewName,compact('item','clientId','pageVariables'));
		}
		else if(!$this->showOnlyYourOwnData)
		{
			$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		$pageVariables=$this->editPageLoad($request);
		return view($this->editViewName,compact('item','clientId','pageVariables'));
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
		$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		$pageVariables=$this->editPageLoad($request);
		return view($this->editViewName,compact('item','clientId','pageVariables'));
		}
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
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
		if($this->isLoginRequired)
		{
			$user =$request->session()->get("userDetails");
			$data =$this->service->get($id);
			if($this->isAuthorized($clientId, 'update',$request,  $user,$data ))
			{
					$this->processRequestBeforeSaveOrUpdate($request);
					$request->merge(['clientId'=>$clientId]);
					$item=$this->service->update($id, $request->all());
					$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
					$this->processResponseBeforeView($item);
					$pageVariables=$this->editPageLoad($request);
		         return view($this->editViewName,compact('clientId','item','pageVariables'));
			}
			else{
				return response(null, 401);
			}
		}
		else{
		$this->processRequestBeforeSaveOrUpdate($request);
		$request->merge(['clientId'=>$clientId]);
		$item=$this->service->update($id, $request->all());
		$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
		$this->processResponseBeforeView($item);
		$pageVariables=$this->editPageLoad($request);
		return view($this->editViewName,compact('clientId','item','pageVariables'));
		}	
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($clientId, $id, Request $request)
	{

		if($this->isLoginRequired)
		{
			$user =$request->session()->get("userDetails");
			$data=$this->service->get($id);
			if($this->isAuthorized($clientId, 'delete', $request,  $user,$data ))
			{
				 $this->service->delete($id);
				 $request->session()->flash('alert-success', 'Your '.$this->modelName.' deleted successfully!');
			 	return $this->index($clientId, $request);
			}
			else{
				return response(null, 401);
			}
		}
		else{
		 $this->service->delete($id);
		 $request->session()->flash('alert-success', 'Your '.$this->modelName.' deleted successfully!');
		 return $this->index($clientId, $request);
		}
	}
	
	protected $editViewName;
	protected $viewViewName;
	protected $searchViewName;
	protected $modelName;
	protected $validations;
		
}