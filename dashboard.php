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
      height: 44px;
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
    ?>

    <main>
      <div class="wrapper text-xl">Welcome, <span class="capitalize"><?= $user->name ?></span>!</div>

      <form method="post" action="/actions/add_todo.php">
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
                <form method="post" action="/actions/toggle_todo.php">
                  <input type="hidden" name="todo_id" value="<?= $todo->id ?>">
                  <label class="checkbox">
                    <input type="checkbox" name="checked" onchange="this.form.submit()" <?= $todo->checked ? 'checked' : '' ?>>
                    <span class="checkmark"></span>
                  </label>
                </form>
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