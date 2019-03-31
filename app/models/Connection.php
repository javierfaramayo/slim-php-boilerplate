<?php
namespace App\Models;

use PDO;

abstract class Connection {
  // Attributes
  private static $db_host = DDBB_HOST;
  private static $db_user = DDBB_USER;
  private static $db_pass = DDBB_PASS;
  private static $db_name = DDBB_NAME;
  private $conn;
  protected $query;
  protected $rows;
  protected $class;
  protected $values = [];

  // Open a database connection
  private function dbOpen()
  {
    try{

      $this -> conn = new PDO("mysql:host=".self::$db_host.";dbname=".self::$db_name.";", self::$db_user , self::$db_pass);
      $this -> conn -> exec("set names utf8");
      $this -> conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch(PDOException $e) {

      return $e -> getMessage();

    }
  }

  // Close database connection
  private function dbClose()
  {
    $this -> conn = null;
  }

  // Open connection and prepare a query
  protected function initQuery()
  {
    $this -> dbOpen();
    $stmt = $this -> conn -> prepare($this -> query);
    return $stmt;
  }

  // Open connection, initialize a transaction and prepare the query
  protected function setTransaction()
  {
    $this -> dbOpen();
    $this -> conn -> beginTransaction();
    $stmt = $this -> conn -> prepare($this -> query);
    return $stmt;
  }

  // Commit a transaction
  protected function finishTransaction($stmt)
  {
    $stmt -> execute();
    $id = $this -> conn -> lastInsertId();

    $this -> conn -> commit();
    $stmt = null;
    $this -> dbClose();

    return $id;
  }

  // Insert a new ROW and get the inserted id
  protected function insertAndGetId()
  {
    try {
      $this -> dbOpen();
      $this -> conn -> beginTransaction();
      $stmt = $this -> conn -> prepare($this -> query);
      $stmt = $this -> bindParams($stmt);
      $stmt -> execute();
      $id = $this -> conn -> lastInsertId();

      $this -> conn -> commit();
      $stmt = null;
      $this -> dbClose();

      return ['response' => 'ok', 'id' => $id];

    } catch (\PDOException $e) {

      $this -> conn -> rollBack();
      return ['response' => 'error', 'message' => $e -> getMessage()];
    }
  }

  // Execute a query and returns the result as an array of objects of the given class, in case of using RAW mode the result will be returned as an associated array
  protected function getQuery( $fetch = 'fetchAll' )
  {
    try {

      $this -> dbOpen();
      $stmt = $this -> conn -> prepare($this -> query);
      $stmt = $this -> bindParams($stmt);
      $stmt -> execute();

      if(!empty($this -> class)){

        $stmt -> setFetchMode(PDO::FETCH_CLASS, $this -> class);
      } else {

        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
      }

      $this -> rows = $stmt -> $fetch();

      $stmt = null;

      $this -> dbClose();

      if(is_array($this->rows)){

        return (count($this -> rows) > 0) ? $this -> rows : false;
      }

      return $this -> rows;

    } catch (\PDOException $e) {

      return false;
    }
  }

  // Execute a query with "SQL_CALC_FOUND_ROWS" in the statement to get the total number of rows existing in the table, the result will be an array with 2 fields: "data" where will be the rows returned from database, and "found_rows" where will be the number of rows in database. This function is recomended to use with a LIMIT clause in query to paginate results.
  protected function getFoundRows( $fetch = 'fetchAll' )
  {
    try {

      $this -> dbOpen();
      $stmt = $this -> conn -> prepare($this -> query);
      $stmt = $this -> bindParams($stmt);
      $stmt -> execute();

      if(!empty($this -> class)){

        $stmt -> setFetchMode(PDO::FETCH_CLASS, $this -> class);
      } else {

        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
      }

      $this -> rows['data'] = $stmt -> $fetch();

      $stmt = $this -> conn -> query("SELECT FOUND_ROWS()");
      $this -> rows['found_rows'] = $stmt -> fetchColumn();

      $stmt = null;

      $this -> dbClose();

      return $this -> rows;

    } catch (\PDOException $e) {

      return false;
    }
  }

  // Execute a query that returns an array containing the result of the execution, it can be "ok" or "error", in the last case will be returned the exception message in a field named "message".
  protected function setQuery()
  {
    try {

      $this -> dbOpen();
      $stmt = $this -> conn -> prepare($this -> query);
      $stmt = $this -> bindParams($stmt);
      $stmt -> execute();

      $stmt = null;
      $this -> dbClose();

      return ['response' => 'ok'];

    } catch (\PDOException $e) {

      return ['response' => 'error', 'message' => $e -> getMessage()];
    }
  }

  // Method to bind params in a query, receives the object of connection and verifies if exists anything in the property "values" to bind, then do the binding, if "values" is empty returns the same object without changes.
  private function bindParams( $stmt )
  {
    if(count($this->values) > 0){
      foreach($this->values as $key => &$value)
      {
        $stmt -> bindParam($key, $value);
      }
    }
    return $stmt;
  }
}