<?php

class Todo
{
  public function __construct(
    public int $id,
    public int $user_id,
    public string $text,
    public bool $checked,
    public DateTime $updated_at,
    public DateTime $created_at,
  ) {
  }

  public static function fromArray(array $row): self
  {
    return new self(
      (int) $row['id'],
      (int) $row['user_id'],
      $row['text'],
      (bool) $row['checked'],
      new DateTime($row['updated_at']),
      new DateTime($row['created_at']),
    );
  }
}

class CreateTodoDTO
{
  public string $user_id;
  public string $text;

  public function __construct(int $user_id, string $text)
  {
    $this->user_id = (int) $user_id;
    $this->text = trim($text);
  }
}

class UpdateTodoDTO
{
  public string $text;
  public string $checked;

  public function __construct(string $text, bool $checked)
  {
    $this->text = trim($text);
    $this->checked = (string) (int) $checked;
  }
}
