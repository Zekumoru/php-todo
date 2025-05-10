<?php

$servername = "localhost";
$username = "root";
$password = "";

try {
  $conn = new PDO("mysql:host=$servername", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $conn->exec("CREATE DATABASE IF NOT EXISTS php_todo");
  $conn->exec("USE php_todo");

  $conn->exec("
    CREATE TABLE IF NOT EXISTS users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      email VARCHAR(255) NOT NULL UNIQUE,
      password VARCHAR(255) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
  ");

  $conn->exec("
    CREATE TABLE IF NOT EXISTS cookies (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      token VARCHAR(255) NOT NULL UNIQUE,
      expiry TIMESTAMP NOT NULL,
      FOREIGN KEY (user_id) REFERENCES users(id)
    )
  ");
} catch (PDOException $e) {
  echo $e->getMessage();
}