<?php require "auth/auth.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.php"; ?>
  <title>Dashboard | PHP Todo</title>
  <style>
    main {
      width: 100%;
    }
  </style>
</head>

<body>
  <div id="app">
    <?php include "components/nav.php"; ?>

    <main>
      <div class="wrapper">Welcome, <span class="capitalize"><?= $user->name ?></span>!</div>
    </main>
  </div>
</body>

</html>