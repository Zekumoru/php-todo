<?php
setcookie('credentials', '', time() - 3600, '/');
header('Location: /index.php');
exit;
