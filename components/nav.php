<?php
// Figure out the current page’s filename
$currentPage = basename($_SERVER['SCRIPT_NAME']);
// Decide which link to show
if ($currentPage === 'sign-up.php') {
  $linkHref = '/index.php';
  $linkText = 'Login';
} else {
  $linkHref = '/sign-up.php';
  $linkText = 'Sign up';
}
?>

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

  .main-nav .nav-links-item a {
    display: inline-block;
    transition: background-color 0.3s;
    padding: 18px 32px;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
  }

  .main-nav .nav-links-item a:hover {
    background-color: var(--color-nav-hover);
  }
</style>

<nav class="main-nav">
  <div class="wrapper">
    <div class="title">PHP Todo</div>

    <ul class="nav-links">
      <li class="nav-links-item"><a href="<?= $linkHref ?>"><?= $linkText ?></a></li>
    </ul>
  </div>
</nav>