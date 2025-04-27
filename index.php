<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.php"; ?>
  <title>PHP Todo</title>
  <style>
    main {
      display: grid;
      place-content: center;
      flex-grow: 1;
    }

    .login-form {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
  </style>
</head>

<body>
  <div id="app">
    <?php include "components/nav.php"; ?>

    <main>
      <form class="login-form" method="post" action="">
        <div class="form-control">
          <label for="email">Email</label>
          <input class="input" type="email" name="email" id="email" />
        </div>

        <div class="form-control">
          <label for="password">Password</label>
          <input class="input" type="password" name="password" id="password" />
        </div>

        <button class="btn btn-primary">Login</button>
      </form>
    </main>
  </div>
</body>

</html>