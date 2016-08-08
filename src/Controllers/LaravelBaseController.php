<?php
namespace Canigenus\CommonPhp\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Canigenus\CommonPhp\Services\ServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

abstract class LaravelBaseController  extends Controller {
	
	protected $service;
	protected $userService;
	protected $canBeUpdatedOnlyByOwner=false;
	protected $showOnlyYourOwnData=false;
	protected $isLoginRequired=true;
	protected $needRoleAuthentication=true;	
	protected $modelName;
	protected $permissionModelName;
	protected $saveNoRistriction=false;
	protected $viewNoRistriction=false;
	
	public function __construct(ServiceInterface $serviceInterface,$editViewName,$searchViewName,$modelName,$validations, $permissionModelName)
	{
		$this->service = $serviceInterface;
		$this-> editViewName=$editViewName;
		$this-> searchViewName=$searchViewName;
		$this-> modelName=$modelName;
		$this-> validations=$validations;
		$this->service = $serviceInterface;
		$this->permissionModelName=$permissionModelName;
	}
	
	public function isAuthorized($function, Request $request,  $user, $data=null){
	 if($this->needRoleAuthentication && !$user->getAttribute('admin')){
		if(!in_array($this->modelName.'_'.$function,$request->session()->get("permissions")))
			{
				return false;
			}
		}
		if($this->canBeUpdatedOnlyByOwner && $data!=null && $data->getAttribute('user_id')!=$user->getAttribute('id'))
		{
			return false;
		}
		if(!$this->isAuthrizedExtra($function, $request,  $user=null, $data=null)){
			return false;
		}
		return true;
	}
		protected function isAuthrizedExtra($function, Request $request,  $user=null, $data=null)
		{
			return true;
		}
	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function search(Request $request)
	{
		$searchCriteria=[];
		$pageVariables=$this->searchPageLoad($request);
		return view($this->searchViewName,compact('searchCriteria','pageVariables'));
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		if($this->isLoginRequired && !$this->viewNoRistriction)
		{
			$user =$request->session()->get("userDetails");
			if($user==null)
				return response(null, 401);
			if($this->isAuthorized('view',$request,$user))
			{
				$items= $this->service->getList($request->all(),15);
				$items->appends($request->except(array('page')));
				$searchCriteria=$request->except(array('page'));
				$pageVariables=$this->searchPageLoad($request);
				return view($this->searchViewName,compact('items','searchCriteria'));
			}
			else{
				return response(null, 401);
			}
		}
		else{
			$items= $this->service->getList($request->all(),15);
			$items->appends($request->except(array('page')));
			$searchCriteria=$request->except(array('page'));
			$pageVariables=$this->searchPageLoad($request);
			return view($this->searchViewName,compact('items','searchCriteria'));
		}
	}
	
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
		$pageVariables=$this->editPageLoad($request);
		return view($this->editViewName,compact('pageVariables'));
	}
	
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{

		if($this->isLoginRequired && !$this->saveNoRistriction)
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
			if($this->isAuthorized('create',$request,$user))
			{
				$this->processRequestBeforeSaveOrUpdate($request);
				$request->merge(['created_by'=>$user->getAttribute("id"), 'updated_by'=>$user->getAttribute("id")]);
				$this->service->save($request->all());
				$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
				$pageVariables=$this->editPageLoad($request);
				return view($this->editViewName, compact("pageVariables"));
			}
			else{
				return response(null, 401);
			}
		}
		else{
			$this->processRequestBeforeSaveOrUpdate($request);
			$request->merge(['created_by'=>$user->getAttribute("id"), 'updated_by'=>$user->getAttribute("id")]);
			$this->service->save($request->all());
			$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
			$pageVariables=$this->editPageLoad($request);
			return view($this->editViewName, compact("pageVariables"));
		}
		
		$valid=  Validator::make($request->all(), $this->validations);
		if($valid->fails())
		{
			return redirect()
			->back()
			->withInput($request->all())
			->withErrors($valid->errors());
		}
	}
	
	public function processRequestBeforeSaveOrUpdate(Request $request){
	}
	public function processResponseBeforeView(Model $response){
	}
	
	public function searchPageLoad(Request $request){
		
	}
	public function editPageLoad(Request $request){
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		return view(isset($this->viewViewName)?$this->viewViewName:$this->editViewName,compact('item'));
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id, Request $request)
	{

		if($this->isLoginRequired)
		{
			$user =$request->session()->get("userDetails");
			$response=$this->service->get($id);
			if($this->isAuthorized('view',$request, $user,$response)){
		
				if($this->showOnlyYourOwnData && !$user->getAttribute('admin')){
					$item=$this->service->get($id);
					$this->processResponseBeforeView($item);
					$pageVariables=$this->editPageLoad($request);
					return view(isset($this->viewViewName)?$this->viewViewName:$this->editViewName,compact('item','pageVariables'));
				}
				else if($this->showOnlyYourOwnData && $response->getAttribute('user_id')==$user->getAttribute('id')){
					$item=$this->service->get($id);
					$this->processResponseBeforeView($item);
					$pageVariables=$this->editPageLoad($request);
					return view(isset($this->viewViewName)?$this->viewViewName:$this->editViewName,compact('item','pageVariables'));
				}
				else if(!$this->showOnlyYourOwnData)
				{
					$item=$this->service->get($id);
					$this->processResponseBeforeView($item);
					$pageVariables=$this->editPageLoad($request);
					return view(isset($this->viewViewName)?$this->viewViewName:$this->editViewName,compact('item','pageVariables'));
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
			return view(isset($this->ediViewName)?$this->viewViewName:$this->editViewName,compact('item','pageVariables'));
		}
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
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
			if($this->isAuthorized('update',$request,  $user,$data ))
			{
				$this->processRequestBeforeSaveOrUpdate($request);
				$item=$this->service->update($id, $request->all());
				$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
				$this->processResponseBeforeView($item);
				$pageVariables=$this->editPageLoad($request);
				return view($this->editViewName,compact('pageVariables','item'));
			}
			else{
				return response(null, 401);
			}
		}
		else{
			$this->processRequestBeforeSaveOrUpdate($request);
			$item=$this->service->update($id, $request->all());
			$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
			$this->processResponseBeforeView($item);
			$pageVariables=$this->editPageLoad($request);
			return view($this->editViewName,compact('pageVariables','item'));
		}
		
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
    public function destroy($id, Request $request)
	{
		if($this->isLoginRequired)
		{
			$user =$request->session()->get("userDetails");
			$data=$this->service->get($id);
			if($this->isAuthorized('delete', $request,  $user,$data ))
			{
				$this->service->delete($id);
				$request->session()->flash('alert-success', 'Your '.$this->modelName.' deleted successfully!');
				return $this->index($request);
			}
			else{
				return response(null, 401);
			}
		}
		else{
			$this->service->delete($id);
			$request->session()->flash('alert-success', 'Your '.$this->modelName.' deleted successfully!');
			return $this->index($request);
		}
	}
	
	protected $editViewName;
	protected $viewViewName;
	protected $searchViewName;
	protected $validations;
	
	
}