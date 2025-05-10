<?php

class User
{
  public function __construct(
    public int $id,
    public string $name,
    public string $email,
  ) {
  }

  public static function fromArray(array $row): self
  {
    return new self(
      (int) $row['id'],
      $row['name'],
      $row['email'],
    );
  }
}

class PasswordUser extends User
{
  public function __construct(
    public int $id,
    public string $name,
    public string $email,
    public string $password
  ) {
  }

  public static function fromArray(array $row): self
  {
    return new self(
      (int) $row['id'],
      $row['name'],
      $row['email'],
      $row['password'],
    );
  }
}

class CreateUserDTO
{
  public string $name;
  public string $email;
  public string $password;

  public function __construct(array $data)
  {
    $this->name = ucwords(trim($data['name']));
    $this->email = strtolower(trim($data['email']));
    $this->password = $data['password'];
  }

  public function hashPassword(): void
  {
    $this->password = password_hash($this->password, PASSWORD_DEFAULT);
  }
}

class LogInUserDTO
{
  public string $email;
  public string $password;

  public function __construct(array $data)
  {
    $this->email = strtolower(trim($data['email']));
    $this->password = $data['password'];
  }

  public function verify(string $hashedPassword): bool
  {
    return password_verify($this->password, $hashedPassword);
  }
}
