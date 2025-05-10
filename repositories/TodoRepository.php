<?php

require_once __DIR__ . '/../models/Todo.php';

class TodoRepository
{
  public function __construct(private PDO $conn)
  {
  }

  public function findById(int $id): ?Todo
  {
    $stmt = $this->conn->prepare("SELECT * FROM todos WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
      return null;
    }
    return Todo::fromArray($row);
  }

  /**
   * @return Todo[]
   */
  public function findAllByUserId(int $id): array
  {
    $stmt = $this->conn->prepare("SELECT * FROM todos WHERE user_id = ? ORDER BY checked ASC, text ASC");
    $stmt->execute([$id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return array_map(fn($row) => Todo::fromArray($row), $rows);
  }

  public function insertOne(CreateTodoDTO $todo): bool
  {
    $stmt = $this->conn->prepare("INSERT INTO todos (user_id, text) VALUES (?, ?)");
    return $stmt->execute([$todo->user_id, $todo->text]);
  }

  public function updateOne(int $id, UpdateTodoDTO $todo): bool
  {
    $stmt = $this->conn->prepare("UPDATE todos SET text = ?, checked = ? WHERE id = ?");
    return $stmt->execute([$todo->text, $todo->checked, $id]);
  }

  public function deleteOne(int $id): bool
  {
    $stmt = $this->conn->prepare("DELETE FROM todos WHERE id = ?");
    return $stmt->execute([$id]);
  }
}
