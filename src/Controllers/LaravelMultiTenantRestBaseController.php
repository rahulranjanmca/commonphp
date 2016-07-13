<?php
namespace Canigenus\CommonPhp\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Canigenus\CommonPhp\Services\ServiceInterface;

class LaravelMultiTenantRestBaseController  extends Controller {
	
	protected  $service;
	
	public function __construct(ServiceInterface $serviceInterface)
	{
		$this->service = $serviceInterface;
	}
	
		
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
	return $this->service->getList($request);
	}
	
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
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
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update($id, $clientId, Request $request)
	{
		return response($this->service->update($id,$entity));
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		return $this->service->delete($id);
	}
	
	
}