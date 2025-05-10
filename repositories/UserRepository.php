<?php

require_once 'models/User.php';

class UserRepository
{
  public function __construct(private PDO $conn)
  {
  }

  public function findById(int $id): ?User
  {
    $stmt = $this->conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
      return null;
    }
    return User::fromArray($row);
  }

  public function findByEmail(string $email): ?PasswordUser
  {
    $stmt = $this->conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
      return null;
    }
    return PasswordUser::fromArray($row);
  }

  public function insertOne(CreateUserDTO $user): bool
  {
    $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    return $stmt->execute([$user->name, $user->email, $user->password]);
  }
}