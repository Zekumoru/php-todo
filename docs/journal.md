# My Journey in Building a PHP Todo Web Application

## The Beginnings

At first, I just wanted to learn PHP. So I went through everything in the [PHP Tutorial](https://www.w3schools.com/php/) website, from the basics all the way to the _MySQL Database_ section. (I had already started the project after finishing the _PHP OOP_ section.)

But of course, theory doesn't stick unless applied. So, the classic web application project to apply what I learned was a Todo application—because it involves authentication, authorization, CRUD operations, and application architecture. Building a fully functional application from scratch was the best way for me to internalize PHP and its nuances.

I chose to use vanilla HTML, no frameworks. Using a framework would have robbed me of the chance to architect the web app from scratch and really understand how PHP works. Learning both a framework and PHP at the same time would have been counterproductive. Also, I wanted to go hardcore.

Having prior experience with application structure, I chose the so-called "flat structure," where similar modules are placed into their own folders with minimal nesting. For example:

```bash
models/
  User.php
  ...
repositories/
  UserRepository.php
  ...
dashboard.php
index.php
...
```

Notice how the files are flat—no deep nesting. This brings up another approach: _feature-based_ structuring, where `utils/`, `models/`, etc., are grouped under `feature-xxx/` folders. But for a simple application like this, there's no need for that.

> If you're looking for sources on what I did, I don't remember where I got them, nor should I care. These are experiences I've gathered over the years from being a fanatic of architecture, SOLID principles, design patterns, etc.

With the folder structure in place, it was time to apply some common architectural patterns to make the codebase scalable. One of those was a partial application of _Clean Architecture_. You’ll notice the `models/` and `repositories/` folders. The models contain the business logic/data, and the repositories handle how to interact with those models—typically through the database. I also used the DTO (Data Transfer Object) pattern to define what each operation in a repository expects. Repositories in my case also act like services which are gateways between the application and the database.

In a full Clean Architecture setup, there would also be "use cases" acting as gateways between the web app and repositories, and the repositories act as gateways to the actual database. Why bother with this level of indirection? Because repositories are database-specific: one might use MySQL, another MongoDB. If you abstract the interaction through use cases, you can easily swap or upgrade your database interactions, even at runtime!

> Did you know this swapping logic is called the Strategy pattern? If you're using classes and inheritance, that's also polymorphism. The Strategy pattern involves creating multiple logic handlers and choosing one at runtime. It aligns with the Open-Closed Principle of SOLID: code should be open for extension but closed for modification. Adding a new strategy shouldn't require changing existing code. The Dependency Inversion Principle (DIP) is also relevant here: modules should depend on abstractions, not concrete implementations. This "decouples" different parts of your code. The Strategy pattern and Dependency Injection are two ways to achieve DIP.

### Example of a Model

```php
class User
{
  public function __construct(
    public int $id,
    public string $name,
    public string $email,
  ) {}
}
```

### Example of a Repository

```php
class UserRepository
{
  public function __construct(private PDO $conn) {}

  public function findById(int $id): ?User { /* ... */ }
  public function findByEmail(string $email): ?PasswordUser { /* ... */ }
  public function insertOne(CreateUserDTO $user): bool { /* ... */ }
}
```

Notice how the model defines what the data is, while the repository defines how to interact with the database regarding this model. The beauty of the repository pattern is that the web application doesn’t need to know anything about the underlying database. This makes scaling much easier. However, these patterns can lead to more redirection and complexity. More indirection means it's harder to trace, but also allows for modular and extendable code. This ties back into the Open-Closed Principle and the Single Responsibility Principle (SRP).

When I wrote these models and repositories, I had the TypeScript mindset: thinking of them as interfaces. To me, the `User` model is like a `User` interface. (Don’t confuse this with Java’s interfaces.)

At that time, I wasn’t yet using MySQL. I was saving data using JSON files. But the switch was easy because all I had to change was the `UserRepository` class. Isn’t that great?

## Implementing Registration and Login

The first functionality I implemented was user registration and login. Here's an excerpt from the `sign-up.php` page:

```php
<?php
  require "utils/sanitize.php";
  require "models/User.php";

  // Here we have an empty form
  $name = "";
  $email = "";
  $password = "";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filePath = __DIR__ . "/data/users.json";

    // We take the existing users from our JSON database
    // for validating if an email has already been registered
    $users = json_decode(file_get_contents($filePath), true) ?: [];

    // We then check the validate and sanitize the user's inputs
    $name = sanitize($_POST["name"]);
    if (empty($name)) {
      $nameErr = "Name is required";
    } elseif (!preg_match("/^[a-zA-Z-]*$/", $name)) {
      $nameErr = "Only letters and dashes allowed";
    }

    $email = sanitize($_POST["email"]);
    if (empty($email)) {
      $emailErr = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email";
    } else {
      foreach ($users as $user) {
        if (isset($user["email"]) && $user["email"] === strtolower($email)) {
          $emailErr = "Email already registered";
          break;
        }
      }
    }

    $password = sanitize($_POST["password"]);
    if (empty($password)) {
      $passwordErr = "Password is required";
    } elseif (strlen($password) < 8) {
      $passwordErr = "Password must be at least 8 characters";
    } elseif (!preg_match("/^[a-zA-Z0-9-]*$/", $password)) {
      $passwordErr = "Only letters, numbers and dashes allowed";
    }

    // And if all goes well then we create a new user and redirect them to the login page (which is index.php)
    if (!isset($nameErr) && !isset($emailErr) && !isset($passwordErr)) {
      $user = new User(strtolower($name), strtolower($email), $password);
      $users[] = $user->to_array();
      file_put_contents($filePath, json_encode($users, JSON_PRETTY_PRINT));

      header("Location: /index.php");
      exit;
    }
  }
?>
```

Login followed the same logic: get data from JSON, validate, redirect to dashboard. But to maintain session state, I used cookies:

```php
// auth/auth.php
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
```

A cookie is a small piece of data sent with every request. This code was included in pages that needed access control.

## Switching to MySQL with PDO

At this point, I had registration/login and a simple dashboard. Before switching to MySQL, I studied it using the W3Schools MySQL section and some videos. I chose to use PDO (PHP Data Objects) over MySQLi because:

- PDO supports many database systems.
- It has better exception handling.

I had learned MySQLi first, then moved to PDO. Converting from JSON to database was straightforward thanks to my repository abstraction.

Passwords were initially saved as plain text—bad practice. So I started using PHP’s native `password_hash()` before saving passwords.

Here’s the old login cookie code:

```php
# index.php

# if there's no error to login then set cookie that the user has logged in containing their credentials
if (!isset($err)) {
  $cookieUser = ["name" => $user->name, "email" => $user->email];
  // expire credentials cookie in 1 week
  setcookie("credentials", json_encode($user), time() + 86400 * 7, "/");
  header("Location: /dashboard.php");
  exit;
}
```

Which is fine, but if a user checks their browser's cookies, they'll see the data in plain text. To avoid this, many sites use JWTs (JSON Web Tokens). A JWT takes the data and signs it with a secret key. This doesn’t encrypt the data, it’s still visible but the signature at the end ensures the data hasn’t been tampered with. When the server receives the token, it recalculates the signature and checks if it matches. If it does, the data is trusted and the user is considered legit. JWTs are great because they contain the session themselves, the server does not need to save sessions nor the client. However, in this PHP Todo web app, I opted to a session approach. (I'm not talking about browser sessions or `$_SESSION` which gets reset on browser's close.)

In this approach, before setting the credentials cookie, it is encrypted first and saved in a database. We need to save it because encrypting the cookie, we lose the ability to decrypt it. When a signed in user wants to access the website, we can check their cookie against the cookie saved in the database and if they match then they're legit:

```php
# index.php

# if there's no error to login then set cookie that the user has logged in containing their credentials
if (!isset($err)) {
  # now we have a cookie repository which communicates with our database
  $cookieRepository = new CookieRepository($conn);

  $cookieRaw = ["name" => $user->name, "email" => $user->email];
  $expiry = new DateTime("+7 days");

  # notice here that now we encrypt the cookie
  $token = password_hash(json_encode($cookieRaw), PASSWORD_BCRYPT);

  # then save it
  $cookieDto = new CreateCookieDTO($user->id, $token, $expiry);
  $cookieRepository->insertOne($cookieDto);

  setcookie("credentials", $token, $expiry->getTimestamp(), "/");
  header("Location: /dashboard.php");
  exit;
}
```

## Adding Todos

With authentication in place, I added todos. Initially, everything was handled inside `dashboard.php`, but that wasn't scalable. So I moved to a REST-like approach using action routes like `actions/add_todo.php`.

Proper REST would be `POST /api/todo`, `GET /api/todos`, etc., but I didn’t want to deal with JavaScript’s `fetch()`.

Here’s an example:

```php
// actions/add_todo.php
$todoRepository = new TodoRepository($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = trim($_POST["input"]);
  if (!empty($input)) {
    $todo = new CreateTodoDTO($user->id, $input);
    $todoRepository->insertOne($todo);
  }
}

header("Location: /dashboard.php");
```

And that’s how I built the project. Creating a "big" learning project like this taught me a lot of PHP tricks and nuances which I wouldn’t have discovered otherwise.
