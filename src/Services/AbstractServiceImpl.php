<?php
namespace Canigenus\CommonPhp\Services;
use Canigenus\CommonPhp\Repositories\RepositoryInterface;
class AbstractServiceImpl implements ServiceInterface {
	
	protected $repository;
	
	public function __construct(RepositoryInterface $repositoryInterface)
	{
		$this->repository = $repositoryInterface;
	}
	public function getList($criteria, $perPage = 15, $columns = array('*')) {
		return $this->repository->getList($criteria,$perPage, $columns);
	}
	public function save($entity) {
		return $this->repository->save($entity);
	}
	public function update($id, $entity) {
		return $this->repository->update($id, $entity);
	}
	public function delete($id) {
		return $this->repository->delete($id);
	}
	public function getPartialEntity($id, $columns = array('*')) {
		return $this->repository->getPartialEntity($id,$columns);
	}
	public function getEntityByKeyAndValue($field, $value, $columns = array('*')) {
		return $this->repository->getEntityByKeyAndValue($field, $value,$columns);
	}
	/* public function get($field, $value, $columns = array('*')) {
		return $this->repository->get($field, $value,$columns);
	} */
	public function get($id){
		return $this->repository->get($id);
	}
}