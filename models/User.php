<?php
include "interfaces/Arrayable.php";

class User implements Arrayable
{
  public $name;
  public $email;
  public $password;

  public function __construct($name, $email, $password)
  {
    $this->name = $name;
    $this->email = $email;
    $this->password = $password;
  }

  public static function fromJSON($json)
  {
    $user = json_decode($json, true);
    return new User($user["name"], $user["email"], $user["password"]);
  }

  public function to_array()
  {
    return [
      'name' => $this->name,
      'email' => $this->email,
      'password' => $this->password,
    ];
  }
}
