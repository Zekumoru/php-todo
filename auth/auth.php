<?php
$currentPage = basename($_SERVER['SCRIPT_NAME']);

if (isset($_COOKIE["credentials"]) && $currentPage !== 'dashboard.php') {
  header("Location: /dashboard.php");
  exit;
}

if (!isset($_COOKIE["credentials"]) && $currentPage === 'dashboard.php') {
  header("Location: /index.php");
  exit;
}
