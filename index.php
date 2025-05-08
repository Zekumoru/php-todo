<?php require "auth/auth.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.php"; ?>
  <title>PHP Todo</title>
  <style>
    main {
      display: grid;
      align-items: center;
    }
  </style>
</head>

<body>
  <div id="app">
    <?php include "components/nav.php"; ?>

    <?php
    require_once "models/User.php";
    require_once "repositories/UserRepository.php";

    $email = '';
    $password = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $userRepository = new UserRepository($conn);
      $userDto = new LogInUserDTO($_POST);
      $email = $userDto->email;
      $password = $userDto->password;

      $user = $userRepository->findByEmail($email);

      if ($user === null) {
        $err = "Invalid credentials";
      }

      if ($user && $user->password !== $password) {
        $err = "Invalid credentials";
      }

      if (!isset($err)) {
        $cookieUser = ["name" => $user->name, "email" => $user->email];
        // expire credentials cookie in 1 week
        setcookie("credentials", json_encode($user), time() + 86400 * 7, "/");
        header("Location: /dashboard.php");
        exit;
      }
    }
    ?>

    <main>
      <form class="form-container" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="form-control">
          <label for="email">Email</label>
          <input class="input" type="email" name="email" id="email" value="<?= $email ?>" />
        </div>

        <div class="form-control">
          <label for="password">Password</label>
          <input class="input" type="password" name="password" id="password" value="<?= $password ?>" />
        </div>

        <?= isset($err) ? "<span class=\"error\">*$err</span>" : '' ?>
        <button class="btn btn-primary">Login</button>
      </form>
    </main>
  </div>
</body>

</html>