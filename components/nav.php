<style>
  .main-nav {
    background-color: var(--color-nav);
    color: var(--color-nav-text);
  }

  .main-nav .wrapper {
    display: flex;
    align-items: center;
    max-width: var(--screen-lg);
    margin-inline: auto;
  }

  .main-nav .title {
    padding-inline: 16px;
  }

  .main-nav .nav-links {
    margin-left: auto;
  }

  .main-nav .nav-links-item {
    transition: background-color 0.3s;
    padding: 18px 32px;
    cursor: pointer;
  }

  .main-nav .nav-links-item:hover {
    background-color: var(--color-nav-hover);
  }
</style>

<nav class="main-nav">
  <div class="wrapper">
    <div class="title">PHP Todo</div>

    <ul class="nav-links">
      <li class="nav-links-item">Sign up</li>
    </ul>
  </div>
</nav>