<?php

class User
{
  public function __construct(
    public string $name,
    public string $email,
    public string $password
  ) {
  }

  public static function fromDTO(CreateUserDTO $dto): self
  {
    return new self(
      $dto->name,
      $dto->email,
      $dto->password,
    );
  }

  public static function fromJSON($json)
  {
    $user = json_decode($json, true);
    return new User($user["name"], $user["email"], $user["password"]);
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
