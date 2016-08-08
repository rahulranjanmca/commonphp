<?php
namespace Canigenus\CommonPhp\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Canigenus\CommonPhp\Services\ServiceInterface;
use Illuminate\Support\Facades\Validator;

class LaravelRestBaseController  extends Controller {
	
	protected  $service;
	
	public function __construct(ServiceInterface $serviceInterface, $validations)
	{
		$this->service = $serviceInterface;
		$this->validations=$validations;
	}
	
		
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		return response( $this->service->getList($request));
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$valid=  Validator::make($request->all(),$this->validations);
		if($valid->fails())
		{
			return response([
					'message' => 'validation_faild',
					'errors' => $valid->errors()
			],400);
		}
		return response($this->service->save($request->all()),200);
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		return response($this->service->get($id));
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		return response($this->service->get($id),200);
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
			return response([
					'message' => 'validation_faild',
					'errors' => $valid->errors()
			],400);
		}
		return response($this->service->update($id,$request->all()),200);
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		return response($this->service->delete($id),200);
	}
	
	protected $validations;
}