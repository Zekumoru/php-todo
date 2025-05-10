<?php require "auth/auth.php"; ?>
<?php require "utils/error.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.php"; ?>
  <title>PHP Todo</title>
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
    require_once "models/User.php";
    require_once "repositories/UserRepository.php";
    require_once "repositories/CookieRepository.php";

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

      if ($user && !$userDto->verify($user->password)) {
        $err = "Invalid credentials";
      }

      if (!isset($err)) {
        $cookieRepository = new CookieRepository($conn);

        $cookieRaw = ["name" => $user->name, "email" => $user->email];
        $expiry = new DateTime("+7 days");
        $token = password_hash(json_encode($cookieRaw), PASSWORD_BCRYPT);

        $cookieDto = new CreateCookieDTO($user->id, $token, $expiry);
        $cookieRepository->insertOne($cookieDto);

        // expire credentials cookie in 1 week
        setcookie("credentials", $token, $expiry->getTimestamp(), "/");
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