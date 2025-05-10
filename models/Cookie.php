<?php

class Cookie
{
  public function __construct(
    public int $id,
    public int $user_id,
    public string $token,
    public DateTime $expiry,
  ) {
  }

  public static function fromArray(array $row): self
  {
    return new self(
      (int) $row['id'],
      (int) $row['user_id'],
      $row['token'],
      new DateTime($row['expiry']),
    );
  }
}

class CreateCookieDTO
{
  public string $user_id;
  public string $token;
  public string $expiry;

  public function __construct(int $user_id, string $token, DateTime $expiry)
  {
    $this->user_id = (string) $user_id;
    $this->token = $token;
    $this->expiry = $expiry->format('Y-m-d H:i:s');
  }
}
