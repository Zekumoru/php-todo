<?php

require_once 'models/User.php';

class UserRepository
{
  public function __construct(private PDO $conn)
  {
  }

  public function findByEmail(string $email): ?User
  {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
      return null;
    }
    return new User($row['name'], $row['email'], $row['password']);
  }

  public function insertOne(CreateUserDTO $user): bool
  {
    $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    return $stmt->execute([$user->name, $user->email, $user->password]);
  }
}