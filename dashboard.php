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

    .btn {
      width: 48px;
    }

    .btn-add {
      line-height: 1rem !important;
    }

    .todos-container {
      flex-grow: 1;
      overflow-y: auto;
      height: 0;
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
      gap: 8px;
    }

    .todo-text {
      flex-grow: 1;
    }

    .update-container {
      display: flex;
      gap: 8px;
    }

    .todo-input {
      margin-inline-start: -8px;
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

      <form method="post" action="/actions/add_todo.php" onsubmit="handleAddTodo(event)">
        <div class="form-control">
          <input class="input" name="input" type="text" placeholder="What are you up to today?" />
          <button class="btn btn-primary btn-add text-xl">+</button>
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
                <span class="todo-text"><?= $todo->text ?></span>
                <form class="update-container" method="post" action="/actions/update_todo.php">
                  <input type="hidden" name="todo_id" value="<?= $todo->id ?>">
                  <input type="text" class="input todo-input" name="todo_input" value="<?= $todo->text ?>"
                    style="display: none;" />
                  <button type="button" class="btn btn-edit fa-solid fa-edit" onclick="enterEditMode(this)"></button>
                  <button type="button" class="btn btn-cancel fa-solid fa-x" onclick="cancelEdit(this)"
                    style="display: none;"></button>
                  <button type="submit" class="btn btn-submit fa-solid fa-check" style="display: none;"></button>
                </form>
                </form>
                <form class="delete-container" method="post" action="/actions/delete_todo.php">
                  <input type="hidden" name="todo_id" value="<?= $todo->id ?>">
                  <button class="btn fa-solid fa-trash"></button>
                </form>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <script>
    function handleAddTodo(event) {
      const inputEl = event.target.querySelector('input');
      if (!inputEl.value.trim()) event.preventDefault();
    }

    function enterEditMode(button) {
      const todoEl = button.closest('li.todo');

      const textEl = todoEl.querySelector('.todo-text');
      const inputEl = todoEl.querySelector('.todo-input');
      const checkboxEl = todoEl.querySelector('input[type="checkbox"]');

      textEl.style.display = "none";
      inputEl.style.display = "inline-block";
      checkboxEl.disabled = true;

      const editBtn = todoEl.querySelector('.btn-edit');
      const cancelBtn = todoEl.querySelector('.btn-cancel');
      const submitBtn = todoEl.querySelector('.btn-submit');

      const updateContainer = todoEl.querySelector('.update-container');
      const deleteContainer = todoEl.querySelector('.delete-container');

      editBtn.style.display = "none";
      cancelBtn.style.display = "inline-block";
      submitBtn.style.display = "inline-block";
      deleteContainer.style.display = "none";
      updateContainer.style.flexGrow = '1';
    }

    function cancelEdit(button) {
      const todoEl = button.closest('li.todo');

      const textEl = todoEl.querySelector('.todo-text');
      const inputEl = todoEl.querySelector('.todo-input');
      const checkboxEl = todoEl.querySelector('input[type="checkbox"]');

      textEl.style.display = "inline-block";
      inputEl.style.display = "none";
      checkboxEl.disabled = false;

      const editBtn = todoEl.querySelector('.btn-edit');
      const cancelBtn = todoEl.querySelector('.btn-cancel');
      const submitBtn = todoEl.querySelector('.btn-submit');

      const updateContainer = todoEl.querySelector('.update-container');
      const deleteContainer = todoEl.querySelector('.delete-container');

      editBtn.style.display = "inline-block";
      cancelBtn.style.display = "none";
      submitBtn.style.display = "none";
      deleteContainer.style.display = "inline-block";
      updateContainer.style.flexGrow = '0';

      inputEl.value = textEl.textContent;
    }
  </script>
</body>

</html>