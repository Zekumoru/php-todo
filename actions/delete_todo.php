<?php

require_once __DIR__ . '/../auth/auth.php';
require_once __DIR__ . '/../repositories/TodoRepository.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $todoRepository = new TodoRepository($conn);

  $todo_id = $_POST['todo_id'];
  $todo = $todoRepository->findById($todo_id);

  // Check if user owns todo to prevent attacks
  if ($todo && $todo->user_id === $user->id) {
    $todoRepository->deleteOne($todo->id);
  }
}

header("Location: /dashboard.php");
exit;