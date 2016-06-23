<?php
interface ServiceInterface {
	public function all($columns = array('*'));
	public function getList($criteria, $perPage = 15, $columns = array('*'));
	public function save($entity);
	public function update($entity);
	public function delete($id);
	public function getPartialEntity($id, $columns = array('*'));
	public function getEntityByKeyAndValue($field, $value, $columns = array('*'));
	public function getVo($field, $value, $columns = array('*'));
} 