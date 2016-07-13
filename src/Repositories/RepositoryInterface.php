<?php
namespace  Canigenus\CommonPhp\Repositories;
interface RepositoryInterface {
	public function getList($criteria, $perPage = 15, $columns = array('*'));
	public function save($entity);
	public function update($id, $entity);
	public function delete($id);
	public function getPartialEntity($id, $columns = array('*'));
	public function getEntityByKeyAndValue($field, $value, $columns = array('*'));
	/* public function get($field, $value, $columns = array('*')); */
	public function get($id);
} 