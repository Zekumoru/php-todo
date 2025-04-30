<?php require "auth/auth.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.php"; ?>
  <title>Dashboard | PHP Todo</title>
  <style>
    .wrapper {
      flex-grow: 1;
      text-align: left;
    }
  </style>
</head>

<body>
  <div id="app">
    <?php include "components/nav.php"; ?>

    <?php
    require "models/User.php";

    $user = User::fromJSON($_COOKIE["credentials"]);
    ?>

    <main>
      <div class="wrapper">Welcome, <span class="capitalize"><?= $user->name ?></span>!</div>
    </main>
  </div>
</body>

</html>