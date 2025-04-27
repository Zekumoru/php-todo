<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/reset.css">
  <link rel="stylesheet" href="styles/main.css">
  <link rel="stylesheet" href="styles/components.css">
  <title>PHP Todo</title>
  <style>
    #app {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    main {
      display: grid;
      place-content: center;
      flex-grow: 1;
      max-width: var(--screen-lg);
      margin-inline: auto;
      padding: 16px;
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
          <input class="input" type="password" name="password" id="password">
        </div>

        <button class="btn btn-primary">Login</button>
      </form>
    </main>
  </div>
</body>

</html>