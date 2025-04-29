<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="styles/reset.css">
<link rel="stylesheet" href="styles/main.css">
<link rel="stylesheet" href="styles/components.css">
<style>
  #app {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  main {
    min-width: var(--screen-lg);
    margin-inline: auto;
    padding: 16px;
    flex-grow: 1;
  }

  @media screen and (max-width: 1280px) {
    main {
      min-width: 0;
      margin-inline: initial;
    }
  }
</style>