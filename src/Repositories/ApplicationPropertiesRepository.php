<?php
namespace Canigenus\CommonPhp\Repositories;


use Canigenus\CommonPhp\Repositories\AbstractRepositoryImpl;
use Canigenus\CommonPhp\Models\ApplicationProperties;
class ApplicationPropertiesRepository extends AbstractRepositoryImpl
{
	public function __construct(ApplicationProperties $user) {
		$this->model = $user;
	}
	public function setCriteria($criterias) {
	
		if (!empty($criterias['key'])) {
	
			$this->query->where ( 'key', $criterias['key'] );
		}

		if (!empty($criterias['clientId'])) {
		
			$this->query->where ( 'client_id', $criterias['clientId'] );
		}
		if (!empty($criterias['categories'])) {
			$this->query->where ( 'categories', 'like',  "%".$criterias['categories']."%" );
		}
	}
}