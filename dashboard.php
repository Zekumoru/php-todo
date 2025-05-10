<?php require "auth/auth.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.php"; ?>
  <title>Dashboard | PHP Todo</title>
  <style>
    main {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .form-control {
      flex-direction: row;
    }

    .add-button {
      line-height: 1rem !important;
    }

    .todos-container {
      flex-grow: 1;
    }

    .todos {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .todo {
      display: flex;
      align-items: center;
    }

    .todo span {
      flex-grow: 1;
    }

    .no-todos-label {
      color: var(--color-text-grey);
    }
  </style>
</head>

<body>
  <div id="app">
    <?php include "components/nav.php"; ?>

    <?php
    require_once "repositories/TodoRepository.php";

    $todoRepository = new TodoRepository($conn);
    $todos = $todoRepository->findAllByUserId($user->id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $input = trim($_POST["input"]);
      if (!empty($input)) {
        $todo = new CreateTodoDTO($user->id, $input);
        $todoRepository->insertOne($todo);

        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
      }
    }
    ?>

    <main>
      <div class="wrapper text-xl">Welcome, <span class="capitalize"><?= $user->name ?></span>!</div>

      <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="form-control">
          <input class="input" name="input" type="text" placeholder="What are you up to today?" />
          <button class="btn btn-primary add-button text-xl">+</button>
        </div>
      </form>

      <div class="todos-container">
        <?php if (empty($todos)): ?>
          <p class="no-todos-label">No todos yet.</p>
        <?php else: ?>
          <ul class="todos">
            <?php foreach ($todos as $todo): ?>
              <li class="todo">
                <i class="btn fa-solid fa-x"></i>
                <span><?= $todo->text ?></span>
                <i class="btn fa-solid fa-edit"></i>
                <i class="btn fa-solid fa-trash"></i>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>

</html>