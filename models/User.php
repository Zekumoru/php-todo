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

  public function to_array()
  {
    return [
      'name' => $this->name,
      'email' => $this->email,
      'password' => $this->password,
    ];
  }
}
