<?php
namespace App\Models;

class Model extends Connection {
  protected $primaryKey = 'id';
  protected $table;
  protected $sentence;
  protected $fields;
  protected $from;
  protected $joins;
  protected $create_fields;
  private $where = '';
  private $orderBy = '';
  private $limit = '';

  public function __construct()
  {
    $this -> from = "FROM {$this->table} ";
    $this -> class = get_class($this);
  }

  private function buildQuery()
  {
    $this -> query = $this->sentence . $this->fields . $this->from . $this->joins . $this->where . $this->orderBy . $this->limit;
  }

  private function buildCompare(String $field = null, String $value = null, String $operator = null)
  {
    $random = random_int(1 , 9999);

    if ( empty($this->where) ) {

      $this -> where = " WHERE {$field} {$operator} :{$field}{$random}";

    } else {

      $this -> where .= " AND {$field} {$operator} :{$field}{$random}";
    }

    $this -> values[':'.$field.$random] = $value;
  }

  private function buildCompareOr(String $field = null, String $value = null, String $operator = null)
  {
    $random = random_int(1 , 9999);

    $this -> where .= " OR {$field} {$operator} :{$field}{$random}";

    $this -> values[':'.$field.$random] = $value;
  }

  private function buildCompareIn(String $field = null, Array $values = [], String $operator = null)
  {
    if(is_array($values)){

      $valuesAsParams = "";

      foreach ($values as $value) {
        $random = random_int(1 , 9999);

        $valuesAsParams .= ":{$field}{$random}, ";

        $this -> values[':'.$field.$random] = $value;
      }

      $valuesAsParams = substr($valuesAsParams, 0, -2);

      if ( empty($this->where) ) {

        $this -> where = " WHERE {$field} {$operator} ({$valuesAsParams})";

      } else {

        $this -> where .= " AND {$field} {$operator} ({$valuesAsParams})";
      }
    }
  }

  public function find( Array $ids = [] )
  {
    $fetch = count($ids) === 1 ? 'fetch' : 'fetchAll';
    $this -> sentence = "SELECT ";

    if(count($ids) > 0){

      $ids = implode(',', $ids);

      if(empty($this->where)){
        $this -> where = " WHERE {$this->table}.{$this->primaryKey} IN (:ids)";
      } else {

        $this -> where .= " AND {$this->table}.{$this->primaryKey} IN (:ids)";
      }
      $this->values[':ids'] = $ids;
    }
    $this -> buildQuery();
    return $this -> getQuery($fetch);
  }

  public function foundRows()
  {
    $this -> sentence = "SELECT SQL_CALC_FOUND_ROWS ";

    $this -> buildQuery();
    return $this -> getFoundRows();
  }

  public function first()
  {
    $this -> sentence = "SELECT ";
    $this -> limit = " LIMIT 1";
    $this -> buildQuery();
    return $this -> getQuery('fetch');
  }

  public function props( $fields = [] )
  {
    $this -> fields = implode(', ', $fields).' ';

    return $this;
  }

  public function orderBy( String $field = null, String $type = null)
  {
    $this -> orderBy = " ORDER BY {$field} {$type}";

    return $this;
  }

  public function limit( Int $count = null, Int $offset = null)
  {
    if ( !empty($offset) ) {

      $this -> limit = " LIMIT {$offset}, {$count}";
    } else {
      $this -> limit = " LIMIT {$count}";
    }

    return $this;
  }

  public function where( String $field = null, String $value = null )
  {
    $this -> buildCompare($field, $value, '=');

    return $this;
  }

  public function whereNot( String $field = null, String $value = null )
  {
    $this -> buildCompare($field, $value, '!=');

    return $this;
  }

  public function like( String $field = null, String $value = null )
  {
    $this -> buildCompare($field, $value, 'LIKE');

    return $this;
  }

  public function whereIn( String $field = null, Array $values = [] )
  {
    $this -> buildCompareIn($field, $values, 'IN');

    return $this;
  }

  public function whereNotIn( String $field = null, Array $values = [] )
  {
    $this -> buildCompareIn($field, $values, 'NOT IN');

    return $this;
  }

  public function or( String $field = null, String $value = null )
  {
    $this -> buildCompareOr($field, $value, '=');

    return $this;
  }

  public function orNot( String $field = null, String $value = null )
  {
    $this -> buildCompareOr($field, $value, '!=');

    return $this;
  }

  public function orLike( String $field = null, String $value = null )
  {
    $this -> buildCompareOr($field, $value, 'LIKE');

    return $this;
  }

  public function count()
  {
    $this -> sentence = "SELECT COUNT";
    $this -> fields = "(*)";
    $this -> buildQuery();
    return $this -> getQuery('fetchColumn');
  }

  public function exists()
  {
    return ($this->count() > 0) ? true : false;
  }

  public function notExists()
  {
    return ($this->count() > 0) ? false : true;
  }

  public function create()
  {
    $user = (Array) $this;
    $this -> values = [];

    $this -> sentence = "INSERT INTO {$this->table} SET ";
    $fields = '';

    try {

      foreach ($this->create_fields as $field) {

        $fields .= "{$field} = :{$field} , ";

        $this -> values[':'.$field] = $user[$field];
      }
    } catch (\Exception $e) {

      return ['response' => 'error', 'message' => $e -> getMessage()];
    }

    $fields = substr($fields, 0, -2);
    $this -> query = $this -> sentence . $fields;
    return $this -> insertAndGetId();
  }

  public function createAndGet()
  {
    $response = $this -> create();

    if($response['response'] == 'error'){
      return $response;
    }

    $this -> values = [];
    $user = $this -> find([$response['id']]);
    return $user;
  }

  public function update( Array $data = [], $id = null )
  {
    if(!is_numeric($id)){
      return ['response' => 'error', 'message' => 'Id is invalid, must be numeric'];
    }

    if(count($data) == 0){
      return ['response' => 'error', 'message' => 'You cant send empty data to update'];
    }

    if(!$this->find([$id])){
      return ['response' => 'error', 'message' => 'The id you are trying to update doesnt exist'];
    }

    $this -> sentence = "UPDATE {$this->table} SET ";
    $this -> values = [];
    $fields = '';

    foreach ($data as $field => $value) {

      $fields .= "{$field} = :{$field}, ";

      $this -> values[':'.$field] = $value;
    }

    $fields = substr($fields, 0, -2);
    $this -> query = $this -> sentence . $fields . " WHERE {$this->primaryKey} = $id";

    return $this -> setQuery();
  }

  public function updateAndGet( Array $data = [], $id = null )
  {
    $response = $this -> update($data, $id);

    if($response['response'] == 'error'){
      return $response;
    }

    $this -> values = [];
    $user = $this -> find([$id]);
    return $user;
  }

  public function delete( $id = null )
  {
    if(!is_numeric($id)){
      return ['response' => 'error', 'message' => 'Id is invalid, must be numeric'];
    }

    if(!$this->find([$id])){
      return ['response' => 'error', 'message' => 'The id you are trying to delete doesnt exist'];
    }

    $this -> sentence = "DELETE ";
    $this -> query = $this -> sentence . $this -> from . "WHERE {$this->primaryKey} = $id";

    return $this -> setQuery();

  }

  // This function is used to save fake users.
  public function store( String $values = null )
  {
    $fields = implode(', ', $this->create_fields);

    $this -> query = "INSERT INTO {$this->table} ({$fields}) VALUES {$values}";

    return $this -> setQuery();
  }

  public function getRawQuery( String $query = null, Array $params = [], String $fetch = 'fetchAll' )
  {
    $this -> query = $query;
    $this -> values = $params;
    $this -> class = null;

    return $this -> getQuery($fetch);
  }

  public function setRawQuery( String $query = null, Array $params = [] )
  {
    $this -> query = $query;
    $this -> values = $params;
    $this -> class = null;

    return $this -> setQuery();
  }

  public function insertRawQuery( String $query = null, Array $params = [] )
  {
    $this -> query = $query;
    $this -> values = $params;
    $this -> class = null;

    return $this -> insertAndGetId();
  }
}