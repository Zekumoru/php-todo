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
    $email = $_POST["email"];
    $password = $_POST["password"];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $usersPath = __DIR__ . "/data/users.json";

      $users = json_decode(file_get_contents($usersPath), true) ?: [];

      $user = null;
      foreach ($users as $u) {
        if (isset($u['email']) && strtolower($u['email']) === strtolower($email)) {
          $user = $u;
          break;
        }
      }

      if ($user === null) {
        $err = "Invalid credentials";
      }

      if (isset($user["password"]) && $user["password"] !== $password) {
        $err = "Invalid credentials";
      }

      if (!isset($err)) {
        unset($user["password"]);
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