<?php

require_once "models/Todo.php";

class TodoRepository
{
  public function __construct(private PDO $conn)
  {
  }

  /**
   * @return Todo[]
   */
  public function findAllByUserId(int $id): array
  {
    $stmt = $this->conn->prepare("SELECT * FROM todos WHERE user_id = ? ORDER BY text ASC");
    $stmt->execute([$id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return array_map(fn($row) => Todo::fromArray($row), $rows);
  }

  public function insertOne(CreateTodoDTO $todo): bool
  {
    $stmt = $this->conn->prepare("INSERT INTO todos (user_id, text) VALUES (?, ?)");
    return $stmt->execute([$todo->user_id, $todo->text]);
  }
}
