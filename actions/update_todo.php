<?php

require_once __DIR__ . '/../auth/auth.php';
require_once __DIR__ . '/../repositories/TodoRepository.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $todo_input = trim($_POST["todo_input"]);

  if (!empty($todo_input)) {
    $todoRepository = new TodoRepository($conn);

    $todo_id = $_POST['todo_id'];
    $todo = $todoRepository->findById($todo_id);

    // Check if user owns todo to prevent attacks
    if ($todo && $todo->user_id === $user->id) {
      $dto = new UpdateTodoDTO($todo_input, $todo->checked);
      $todoRepository->updateOne($todo->id, $dto);
    }
  }
}

header("Location: /dashboard.php");
exit;