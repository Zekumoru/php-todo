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

    .todos {
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $input = $_POST["input"];
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

      <div class="todos">
        <p class="no-todos-label">No todos yet.</p>
      </div>
    </main>
  </div>
</body>

</html>