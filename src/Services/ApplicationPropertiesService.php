<?php
namespace Canigenus\CommonPhp\Services;
use Canigenus\CommonPhp\Repositories\ApplicationPropertiesRepository;
use Canigenus\CommonPhp\Services\AbstractServiceImpl;
class ApplicationPropertiesService extends AbstractServiceImpl
{
	
	public function __construct(ApplicationPropertiesRepository $repositoryInterface)
	{
		$this->repository = $repositoryInterface;
	}
	
}