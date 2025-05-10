<?php

require_once __DIR__ . '/../auth/auth.php';
require_once __DIR__ . '/../repositories/TodoRepository.php';

$todoRepository = new TodoRepository($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = trim($_POST["input"]);
  if (!empty($input)) {
    $todo = new CreateTodoDTO($user->id, $input);
    $todoRepository->insertOne($todo);
  }
}

header("Location: /dashboard.php");
exit;