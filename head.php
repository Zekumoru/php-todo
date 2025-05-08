<?php require "db/conn.php"; ?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="styles/reset.css">
<link rel="stylesheet" href="styles/main.css">
<link rel="stylesheet" href="styles/utilities.css">
<link rel="stylesheet" href="styles/components.css">
<style>
  #app {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  main {
    width: 100%;
    max-width: var(--screen-lg);
    box-sizing: border-box;
    margin-inline: auto;
    padding: 16px;
    flex-grow: 1;
  }
</style>