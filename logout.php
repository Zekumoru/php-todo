<?php

require "utils/error.php";
require_once "db/conn.php";
require_once "repositories/CookieRepository.php";

if (isset($_COOKIE["credentials"])) {
  $cookie = $_COOKIE["credentials"];
  $cookieRepository = new CookieRepository($conn);
  $cookieRepository->deleteByToken($cookie);
}

setcookie('credentials', '', time() - 3600, '/');
header('Location: /index.php');
exit;
