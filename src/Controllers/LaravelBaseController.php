<?php
namespace Canigenus\CommonPhp\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Canigenus\CommonPhp\Services\ServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

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
		$items= $this->service->getList($request->all(),2);
		$items->appends($request->except(array('page','clientId')));
		$searchCriteria=$request->except(array('page','clientId'));
		$pageVariables=$this->searchPageLoad($request);
		return view($this->searchViewName,compact('items','searchCriteria','pageVariables'));
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
		$pageVariables=$this->editPageLoad($request);
		return view($this->editViewName,compact('pageVariables'));
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
		$item=$this->service->get($id);
		$this->processResponseBeforeView($item);
		$pageVariables=$this->editPageLoad($request);
		return view($this->editViewName, compact('item','pageVariables'));
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
		$pageVariables=$this->editPageLoad($request);
		return view($this->editViewName,compact('item','pageVariables'));
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