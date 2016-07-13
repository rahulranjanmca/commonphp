<?php
namespace Canigenus\CommonPhp\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Canigenus\CommonPhp\Services\ServiceInterface;

abstract class LaravelBaseController  extends Controller {
	
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
	public function search(Request $request)
	{
		$searchCriteria=[];
		return view($this->searchViewName,compact('searchCriteria'));
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($clientId, Request $request)
	{
		$items= $this->service->getList($request->all(),2);
		$items->appends($request->except(array('page','clientId')));
		$searchCriteria=$request->except(array('page','clientId'));
		return view($this->searchViewName,compact('items','searchCriteria'));
	}
	
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($clientId)
	{
		return view($this->editViewName);
	}
	
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
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
		$this->service->save($request->all());
		$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
		return view($this->editViewName);
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
	public function edit($id)
	{
		$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		return view($this->editViewName, compact('item'));
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
		$valid=  Validator::make($request->all(), $this->validations);
		if($valid->fails())
		{
			return redirect()
			->back()
			->withInput($request->all())
			->withErrors($valid->errors());
		}
		$this->processRequestBeforeSaveOrUpdate($request);
		$item=$this->service->update($id, $request->all());
		$request->session()->flash('alert-success', 'Your '.$this->modelName.' saved successfully!');
		$this->processResponseBeforeView($item);
		return view($this->editViewName,compact('item'));
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
    public function delete($id, Request $request)
	{
		 $this->service->delete($id);
		 $request->session()->flash('alert-success', 'Your '.$this->modelName.' deleted successfully!');
		 return $this->index($request);
	}
	
	protected $editViewName;
	protected $viewViewName;
	protected $searchViewName;
	protected $modelName;
	protected $validations;
	
	
}