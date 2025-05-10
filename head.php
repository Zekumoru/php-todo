<?php require "db/conn.php"; ?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="styles/reset.css">
<link rel="stylesheet" href="styles/main.css">
<link rel="stylesheet" href="styles/utilities.css">
<link rel="stylesheet" href="styles/components.css">
<script src="https://kit.fontawesome.com/3b64ecc972.js" crossorigin="anonymous" defer></script>
<style>
  #app {
    display: flex;
    flex-direction: column;
    min-height: 100dvh;
  }

  main {
    max-width: var(--screen-lg);
    box-sizing: border-box;
    margin-inline: auto;
    padding: 16px;
    flex-grow: 1;
  }
</style>