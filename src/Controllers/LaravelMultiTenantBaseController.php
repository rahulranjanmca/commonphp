<?php
namespace Canigenus\CommonPhp\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Canigenus\CommonPhp\Services\ServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

abstract class LaravelMultiTenantBaseController  extends Controller {
	
	protected  $service;
	
	public function __construct(ServiceInterface $serviceInterface,$editViewName,$searchViewName,$modelName,$validations)
	{
		$this->service = $serviceInterface;
		$this-> editViewName=$editViewName;
		$this-> searchViewName=$searchViewName;
		$this-> modelName=$modelName;
		$this-> validations=$validations;
		$this->service = $serviceInterface;
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
	$request->merge(['clientId'=>$clientId]);
	$items= $this->service->getList($request->all(),2);
	$items->appends($request->except(array('page','clientId')));
	$searchCriteria=$request->except(array('page','clientId'));
	return view($this->searchViewName,compact('items','clientId','searchCriteria'));
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
		$valid=  Validator::make($request->all(), $this->validations);
		if($valid->fails())
		{
			return redirect()
			->back()
			->withInput($request->all())
			->withErrors($valid->errors());
		}
		$this->processRequestBeforeSaveOrUpdate($request);
		$request->merge(['clientId'=>$clientId]);
		$this->service->save($request->all());
		$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
		return view($this->editViewName, compact("clientId"));
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
		$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		return view(isset($this->viewViewName)?$this->viewViewName:$this->editViewName,compact('item','clientId'));
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($clientId, $id)
	{
		$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		return view($this->editViewName, compact('item', 'clientId'));
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
		
		$valid=  Validator::make($request->all(), $this->validations);
		if($valid->fails())
		{
			return redirect()
			->back()
			->withInput($request->all())
			->withErrors($valid->errors());
		}
		$this->processRequestBeforeSaveOrUpdate($request);
		$request->merge(['clientId'=>$clientId]);
		$item=$this->service->update($id, $request->all());
		$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
		$this->processResponseBeforeView($item);
		return view($this->editViewName,compact('clientId','item'));
	
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function delete($clientId, $id, Request $request)
	{
		 $this->service->delete($id);
		 $request->session()->flash('alert-success', 'Your '.$this->modelName.' deleted successfully!');
		 return $this->index($clientId, $request);
	}
	
	protected $editViewName;
	protected $viewViewName;
	protected $searchViewName;
	protected $modelName;
	protected $validations;
		
}