<?php
namespace Canigenus\CommonPhp\Repositories;

class AbstractRepositoryImpl  implements RepositoryInterface {
	
	
	/**
	 * The repository model
	 *
	 * @var \Illuminate\Database\Eloquent\Model
	 */
	protected $model;
	
	/* public function __construct(Model $model)
	{
		$this->model = $model;
	} */
	

	/**
	 * The query builder
	 *
	 * @var \Illuminate\Database\Eloquent\Builder
	 */
	protected $query;
	/**
	 * Alias for the query limit
	 *
	 * @var int
	 */
	protected $take;
	/**
	 * Array of related models to eager load
	 *
	 * @var array
	 */
	protected $with = array();
	/**
	 * Array of one or more where clause parameters
	 *
	 * @var array
	 */
	protected $wheres = array();
	/**
	 * Array of one or more where in clause parameters
	 *
	 * @var array
	 */
	protected $whereIns = array();
	/**
	 * Array of one or more ORDER BY column/value pairs
	 *
	 * @var array
	 */
	protected $orderBys = array();
	/**
	 * Array of scope methods to call on the model
	 *
	 * @var array
	 */
	protected $scopes = array();
	
	
	public function getList($criteria, $perPage = 15, $columns = array('*')) {
		return $this->repository->getList($criteria,$perPage, $columns);
	}
	public function save($entity) {
		$this->unsetClauses();
		return $this->model->create($data);
	}
	public function update($entity) {
		$this->unsetClauses();
		$model = $this->getById($id);
		$model->update($data);
		return $model;
	}
	public function delete($id) {
		$this->unsetClauses();
		return $this->get($id)->delete();
	}
	public function getPartialEntity($id, $columns = array('*')) {
		return $this->repository->getPartialEntity($id,$columns);
	}
	public function getEntityByKeyAndValue($field, $value, $columns = array('*')) {
		return $this->repository->getEntityByKeyAndValue($field, $value,$columns);
	}
	
	public function get($id){
		$this->unsetClauses();
		$this->newQuery()->eagerLoad();
		return $this->query->findOrFail($id);
	}
	
	

	/**
	 * Add a simple where clause to the query
	 *
	 * @param string $column
	 * @param string $value
	 * @param string $operator
	 *
	 * @return $this
	 */
	public function where($column, $value, $operator = '=')
	{
		$this->wheres[] = compact('column', 'value', 'operator');
		return $this;
	}
	/**
	 * Add a simple where in clause to the query
	 *
	 * @param string $column
	 * @param mixed  $values
	 *
	 * @return $this
	 */
	public function whereIn($column, $values)
	{
		$values = is_array($values) ? $values : array($values);
		$this->whereIns[] = compact('column', 'values');
		return $this;
	}
	/**
	 * Set Eloquent relationships to eager load
	 *
	 * @param $relations
	 *
	 * @return $this
	 */
	public function with($relations)
	{
		if (is_string($relations)) $relations = func_get_args();
		$this->with = $relations;
		return $this;
	}
	/**
	 * Create a new instance of the model's query builder
	 *
	 * @return $this
	 */
	protected function newQuery()
	{
		$this->query = $this->model->newQuery();
		return $this;
	}
	/**
	 * Add relationships to the query builder to eager load
	 *
	 * @return $this
	 */
	protected function eagerLoad()
	{
		foreach($this->with as $relation)
		{
			$this->query->with($relation);
		}
		return $this;
	}
	/**
	 * Set clauses on the query builder
	 *
	 * @return $this
	 */
	protected function setClauses()
	{
		foreach($this->wheres as $where)
		{
			$this->query->where($where['column'], $where['operator'], $where['value']);
		}
		foreach($this->whereIns as $whereIn)
		{
			$this->query->whereIn($whereIn['column'], $whereIn['values']);
		}
		foreach($this->orderBys as $orders)
		{
			$this->query->orderBy($orders['column'], $orders['direction']);
		}
		if(isset($this->take) and ! is_null($this->take))
		{
			$this->query->take($this->take);
		}
		return $this;
	}
	/**
	 * Set query scopes
	 *
	 * @return $this
	 */
	protected function setScopes()
	{
		foreach($this->scopes as $method => $args)
		{
			$this->query->$method(implode(', ', $args));
		}
		return $this;
	}
	/**
	 * Reset the query clause parameter arrays
	 *
	 * @return $this
	 */
	protected function unsetClauses()
	{
		$this->wheres   = array();
		$this->whereIns = array();
		$this->scopes   = array();
		$this->take     = null;
		return $this;
	}
	
}