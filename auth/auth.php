<?php
require_once __DIR__ . '/../db/conn.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../repositories/CookieRepository.php';

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

$isActionPage = str_starts_with($_SERVER['SCRIPT_NAME'], '/actions/');
if ($isAuth && $currentPage !== 'dashboard.php' && !$isActionPage) {
  header("Location: /dashboard.php");
  exit;
}

if (!$isAuth && $currentPage === 'dashboard.php') {
  header("Location: /index.php");
  exit;
}
