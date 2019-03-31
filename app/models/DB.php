<?php
namespace App\Models;

class DB {

  public static function getRawQuery( String $query = null, Array $params = [], String $fetch = 'fetchAll' )
  {
    $model = new Model();
    return $model->getRawQuery($query, $params, $fetch);
  }

  public static function setRawQuery( String $query = null, Array $params = [] )
  {
    $model = new Model();
    return $model->setRawQuery($query, $params);
  }

  public static function insertRawQuery( String $query = null, Array $params = [] )
  {
    $model = new Model();
    return $model->insertRawQuery($query, $params);
  }
}