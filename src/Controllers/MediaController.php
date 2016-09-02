<?php
namespace App\Http\Controllers;

use App\Http\Services\MediaService;
use App\Http\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Services\ProjectService;
use Illuminate\Support\Facades\Validator;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
class MediaController extends LaravelTwoLevelRestBaseController{
	protected $projectService=null;
	protected  $needRoleAuthentication=false;
	public function __construct(MediaService $serviceInterface, UserService $userService, ProjectService $projectService)
	{
		parent::__construct($serviceInterface,$userService, ['type'=>'required', 'file'=>'required'], 'media', 'project');
		$this->projectService=$projectService;
		$this->userService=$userService;
	}
	
	public function update($clientId, $projectId,  $id, Request $request)
	{
		 throw new \Exception('Not supported exception');
	}
	
	protected function isAuthrizedExtra($clientId, $parentId,  $function, Request $request,  $user=null, $data=null)
	{
		if(in_array($parentId, $this->projectService->getProjectsByUserId($user->getAttribute('id'))))
		{
			return true;
		}
		return false;
	}
	public function storeWithMonitorId($clientId, $parentId, $monitorId, Request $request)
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
		if($this->isAuthorized($clientId, $parentId, 'create',$request,$user))
		{
			$request->merge([$this->parentFieldName=>$parentId]);
			$request->merge(['client_id'=>$clientId, 'created_by'=>Authorizer::getResourceOwnerId(), 'updated_by'=>Authorizer::getResourceOwnerId()]);
			$file = $request->file('file');
			$extension = $file->getClientOriginalExtension();
			Storage::disk('local')->put('public/'.$clientId.'/'.$parentId.'/'.$file->getFilename().'.'.$extension,  File::get($file));
			$request->merge(['file_name'=>$file->getFilename().'.'.$extension]);
			$request->merge(['reference_id'=>$monitorId]);
			return response($this->service->save($request->all()),200);
		}
		else{
			return response(null, 401);
		}
		
		
	}
	
	
	public function updateProjectPoster($clientId, $projectId, Request $request)
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
		if($this->isAuthorized($clientId, null, 'create',$request,$user))
		{
			$request->merge(['client_id'=>$clientId, 'created_by'=>Authorizer::getResourceOwnerId(), 'updated_by'=>Authorizer::getResourceOwnerId()]);
			$file = $request->file('file');
			$extension = $file->getClientOriginalExtension();
			Storage::disk('local')->put('public/'.$clientId.'/'.$parentId.'/'.$file->getFilename().'.'.$extension,  File::get($file));
			$request->merge(['file_name'=>$file->getFilename().'.'.$extension]);
			$request->merge(['reference_id'=>$monitorId]);
			$profilePicMedia=DB::trasaction(function (){
			$media=	$this->service->save($request->all());
			$this->projectService->update($id, ['poster_id'=>$media->getAttribute('id')]);
			return $media;
			});
			return response($profilePicMedia,null);
		}
		else{
			return response(null, 401);
		}
	
	
	}
	
	public function store($clientId, $parentId, Request $request)
	{
		return $this->store($clientId, $parentId, null, $request);
	}
	
	
	
}
