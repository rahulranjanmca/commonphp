<?php
namespace Canigenus\CommonPhp\Services;


use Canigenus\CommonPhp\Repositories\WebsiteSettingRepository;
use Canigenus\CommonPhp\Services\AbstractServiceImpl;

class WebsiteSettingService extends AbstractServiceImpl {

	public function __construct(WebsiteSettingRepository $repositoryInterface)
	{
		$this->repository = $repositoryInterface;
	}
	

}