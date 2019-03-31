<?php
namespace App\Models;
use Faker\Factory;
use App\Utils\Tools;

class User extends Model {

  public function __construct()
  {
    $this -> table = 'users';
    $this -> fields = "{$this->table}.*, s.status ";
    $this -> joins = "INNER JOIN status s ON s.id = {$this->table}.status_id ";
    $this -> create_fields = ['name', 'last_name', 'email', 'pass', 'status_id'];

    parent::__construct();
  }

  public function populate( Int $quantity = 1 )
  {
    $faker = Factory::create();

    $values = '';

    for ($i=0; $i < $quantity; $i++) {

      $name = Tools::sanitize($faker->firstName());
      $last_name = Tools::sanitize($faker->lastName());
      $email = Tools::sanitize($faker->email());
      $pass = md5(Tools::sanitize($faker->word()));
      $status = $faker->numberBetween(1, 3);

      $values .= "('{$name}', '{$last_name}', '{$email}', '{$pass}', {$status}), ";
      
    }

    $values = substr($values, 0, -2);

    return $this->store($values);
  }
}