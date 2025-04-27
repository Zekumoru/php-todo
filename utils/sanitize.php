<?php

function sanitize(string $data): string
{
  $data = trim($data);
  $data = htmlspecialchars($data);
  return $data;
}
