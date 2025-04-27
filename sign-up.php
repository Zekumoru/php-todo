<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.php" ?>
  <title>Sign up | PHP Todo</title>
  <style>
    main {
      display: grid;
      place-content: center;
      flex-grow: 1;
    }

    .signup-form {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .error {
      color: var(--color-danger);
    }
  </style>
</head>

<body>
  <div id="app">
    <?php include "components/nav.php"; ?>

    <?php
    require "utils/sanitize.php";
    require "models/User.php";

    $name = "";
    $email = "";
    $password = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $filePath = __DIR__ . "/data/users.json";

      $users = json_decode(file_get_contents($filePath), true) ?: [];

      $name = sanitize($_POST["name"]);
      if (empty($name)) {
        $nameErr = "Name is required";
      } elseif (!preg_match("/^[a-zA-Z-]*$/", $name)) {
        $nameErr = "Only letters and dashes allowed";
      }

      $email = sanitize($_POST["email"]);
      if (empty($email)) {
        $emailErr = "Email is required";
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email";
      } else {
        foreach ($users as $user) {
          if (isset($user["email"]) && $user["email"] === strtolower($email)) {
            $emailErr = "Email already registered";
            break;
          }
        }
      }

      $password = sanitize($_POST["password"]);
      if (empty($password)) {
        $passwordErr = "Password is required";
      } elseif (strlen($password) < 8) {
        $passwordErr = "Password must be at least 8 characters";
      } elseif (!preg_match("/^[a-zA-Z0-9-]*$/", $password)) {
        $passwordErr = "Only letters, numbers and dashes allowed";
      }

      if (!isset($nameErr) && !isset($emailErr) && !isset($passwordErr)) {
        $user = new User(strtolower($name), strtolower($email), $password);
        $users[] = $user->to_array();
        file_put_contents($filePath, json_encode($users, JSON_PRETTY_PRINT));

        header("Location: /index.php");
        exit;
      }
    }
    ?>

    <main>
      <form class="signup-form" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
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