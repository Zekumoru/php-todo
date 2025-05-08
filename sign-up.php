<?php require "auth/auth.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.php" ?>
  <title>Sign up | PHP Todo</title>
  <style>
    main {
      display: flex;
      align-items: center;
      max-width: 520px;
      width: 100%;
    }

    .form-container {
      flex-grow: 1;
    }
  </style>
</head>

<body>
  <div id="app">
    <?php include "components/nav.php"; ?>

    <?php
    require_once "utils/sanitize.php";
    require_once "models/User.php";
    require_once "repositories/UserRepository.php";

    $name = '';
    $email = '';
    $password = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $userRepository = new UserRepository($conn);
      $userDto = new CreateUserDTO($_POST);
      $name = $userDto->name;
      $email = $userDto->email;
      $password = $userDto->password;

      if (empty($name)) {
        $nameErr = "Name is required";
      } elseif (!preg_match("/^[a-zA-Z-]*$/", $name)) {
        $nameErr = "Only letters and dashes allowed";
      }

      if (empty($email)) {
        $emailErr = "Email is required";
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email";
      } else {
        $user = $userRepository->findByEmail($email);
        if ($user) {
          $emailErr = "Email already registered";
        }
      }

      if (empty($password)) {
        $passwordErr = "Password is required";
      } elseif (strlen($password) < 8) {
        $passwordErr = "Password must be at least 8 characters";
      } elseif (!preg_match("/^[a-zA-Z0-9-]*$/", $password)) {
        $passwordErr = "Only letters, numbers and dashes allowed";
      }

      if (!isset($nameErr) && !isset($emailErr) && !isset($passwordErr)) {
        $userRepository->insertOne($userDto);
        header("Location: /index.php");
        exit;
      }
    }
    ?>

    <main>
      <form class="form-container" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="form-control">
          <label for="name">Name</label>
          <input class="input" type="text" name="name" id="name" value="<?= $name ?>" />
          <?= isset($nameErr) ? "<span class=\"error\">*$nameErr</span>" : '' ?>
        </div>

        <div class="form-control">
          <label for="email">Email</label>
          <input class="input" type="text" name="email" id="email" value="<?= $email ?>" />
          <?= isset($emailErr) ? "<span class=\"error\">*$emailErr</span>" : '' ?>
        </div>

        <div class="form-control">
          <label for="password">Password</label>
          <input class="input" type="password" name="password" id="password" value="<?= $password ?>" />
          <?= isset($passwordErr) ? "<span class=\"error\">*$passwordErr</span>" : '' ?>
        </div>

        <button class="btn btn-primary">Sign up</button>
      </form>
    </main>
  </div>
</body>

</html>