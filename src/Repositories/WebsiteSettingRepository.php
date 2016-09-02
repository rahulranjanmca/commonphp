<?php

namespace Canigenus\CommonPhp\Repositories;


use Canigenus\CommonPhp\Models\WebsiteSetting;
use Canigenus\CommonPhp\Repositories\AbstractRepositoryImpl;


class WebsiteSettingRepository extends AbstractRepositoryImpl {
	
	public function __construct(WebsiteSetting $revSharePlan) {
		$this->model = $revSharePlan;
	}
	public function setCriteria($criterias) {
			if (!empty($criterias['code'])) {
				$this->query->where ( 'code', $criterias['code'] );
		     }
		     if (!empty($criterias['clientId'])) {
		     	$this->query->where ( 'id', $criterias['clientId'] );
		     }
	}
}