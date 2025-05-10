<?php
require_once "db/conn.php";
require_once "repositories/UserRepository.php";
require_once "repositories/CookieRepository.php";

$currentPage = basename($_SERVER['SCRIPT_NAME']);

$hasCredentials = isset($_COOKIE["credentials"]);

$isAuth = false;
if ($hasCredentials) {
  $userRepository = new UserRepository($conn);
  $cookieRepository = new CookieRepository($conn);

  $cookie = $cookieRepository->findByToken($_COOKIE["credentials"]);
  $isAuth = !!$cookie;

  $user = $userRepository->findById($cookie->user_id);
}

if ($isAuth && $currentPage !== 'dashboard.php') {
  header("Location: /dashboard.php");
  exit;
}

if (!$isAuth && $currentPage === 'dashboard.php') {
  header("Location: /index.php");
  exit;
}
