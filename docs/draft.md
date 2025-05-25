# Complete Draft of my Journey to create the PHP Todo web application

- [ ] Create journey md
- [ ] Using the journey md, create documentation (granted that I did put everything there)

## The Beginnings

At first, I just wanted to learn about PHP. And so I went ahead and did everything in this website [PHP Tutorial](https://www.w3schools.com/php/) from the basics until everything in the _MySQL Database_ section. (Which I already started the project when I finished the _PHP OOP_ section.)

But of course, theory won't stick if we don't apply it. And therefore the classic web application to apply what I learned is a todo application because building one includes authentication, authorization, CRUD operations, and application architecture. By creating a fully functional application from scratch could really help me to learn PHP and its nuances as well.

I opted in to using vanilla HTML meaning no framework. Otherwise it'll rob me the chance to architecture my web app, to understand PHP, etc. Since it'll mean learning two things at once: A framework and PHP. And also because I want to go hardcore.

Having already prior experience with structuring applications, I chose the so-called "flat structure" where similar modules are put into their own folder with least possible folder nesting. For example:

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

Notice how the files are "flat", in other words, there's no folder under folder which brings me to another point: There are other types of structuring like the _by-feature_ structuring where related `utils/`, `models/`, etc. are grouped together in a `feature-xxx/` folder where there could be many `xxx` features. For a simple application such as this, there's literally no need to apply that structure.

> If you're looking for sources on what I did, I don't know anymore where to find them nor should I care. They are experiences I gathered throughout the years being a fanatic with architecture, SOLID principles, design patterns, etc.

By having a folder structuring in place, now it's time to apply some common patterns to make the codebase scalable, one of which I opted to using in part the so-called _Clean Architecture_. You'll notice the `models` and `repositories` folder. The models contain the business application logic/data while the repositories contain the "how" to interact with these models. I also used the DTO (Data Transfer Object) pattern to let me define what each operation needs in a repository can do. A repository, or in my case they're also "service", is basically the gateway for communicating between my application and the database. In a full-fledged Clean Architecture style, there are also the so-called "use cases" which is actually the gateway between the web application and the repositories while the repositories the gateway between the use cases and the database. Why do something like this? Because repositories are database-specific, one repository is using MySQL while the another could be using MongoDB, by separating the web application from the repositories using use cases, we could swap this database interaction easily and also in runtime if we want! Seeing this PHP Todo app is just using one database, there's no need to go that far.

> Did you know that the ability to swap interfaces is called the Strategy pattern? If you're using classes with inheritance then it's called polymorphism. The Strategy pattern basically means create multiple logic handler and be able to choose one in runtime. This conforms also to the Open-Closed principle of SOLID where code must be open for extensions but closed for modifications meaning adding a new "strategy" should not touch already existing code. While we're at it, there's also the Dependency Inversion Principle in SOLID where modules should depend on abstractions not on concrete implementations because this "decouples" the code between themselves. One way this is achieved is of course through the Strategy pattern and the Dependency Injection technique.

Here's an example of a model:

```php
class User
{
  public function __construct(
    public int $id,
    public string $name,
    public string $email,
  ) {
  }
}
```

And an example of a repository:

```php
class UserRepository
{
  public function __construct(private PDO $conn) { /* ... */}

  public function findById(int $id): ?User { /* ... */}

  public function findByEmail(string $email): ?PasswordUser { /* ... */}

  public function insertOne(CreateUserDTO $user): bool { /* ... */}
}
```

Notice how the model defines what the data is while the repository defines how to interact to the database regarding this model. The beauty of the repository pattern is that the web application does not know anything about the underlying database. And this is good if we ever scale. But of course, each pattern you use inevitably leads to a lot of redirection and maintenance. Redirection means getting in the situation where you depend so much on abstractions and it just gets harder to traverse your code but as always, abstraction lets us write modular code that will be easier to extend in the future and not relying too much on existing code to modify them when we need to change or add something adhering to the Open-Closed principle which of course also leads to the Single Responsibility Principle (SRP).

When I was writing these models and repositories, I had in mind the TypeScript way like I'm used to defining them as interfaces. To me the `User` model is a `User` interface, etc. (Don't confuse it with Java's interfaces.)

Also around this time, I wasn't using MySQL since I haven't learned it yet. I was saving data using JSON files. But it was easy for me to swap later because I know that the code I must change is all in the `UserRepository` class, isn't that great?

Moving on, the first thing I wanted to implement is user registration and user login so I whipped up a simple registration page and a navbar (to switch between the login page and the registration page) with the following PHP logic:

```php
# this is inside the registration sign-up.php page
<?php
  require "utils/sanitize.php";
  require "models/User.php";

  # Here we have an empty form
  $name = "";
  $email = "";
  $password = "";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filePath = __DIR__ . "/data/users.json";

    # We take the existing users from our JSON database
    # for validating if an email has already been registered
    $users = json_decode(file_get_contents($filePath), true) ?: [];

    # We then check the validate and sanitize the user's inputs
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

    # And if all goes well then we create a new user and redirect them to the login page (which is index.php)
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

The code above is taken from an old commit and does not reflect the current code. The logic is quite similar for logging in: when user submits login, grab our JSON database, check if it exists there, and then redirect to the dashboard page. But then... If we move between pages, how do we save the user's session? Through the use of a cookie:

```php
# auth/auth.php
<?php
# Get the current page
$currentPage = basename($_SERVER['SCRIPT_NAME']);

# If the user is logged in then redirect to dashboard.php page if they haven't already.
if (isset($_COOKIE["credentials"]) && $currentPage !== 'dashboard.php') {
  header("Location: /dashboard.php");
  exit;
}

# If the user is not logged in and tried to access dashboard.php page, redirect them to the login page.
if (!isset($_COOKIE["credentials"]) && $currentPage === 'dashboard.php') {
  header("Location: /index.php");
  exit;
}
```

A cookie is basically a small piece of data which is sent to the server for every requests. The code above is imported to every page that must check whether the user is logged in or not and redirect accordingly. (Again, that code does not reflect the current code.)

At this point in time, registration/login and a simple greeting dashboard have been created and it is also the time when I switched over to MySQL for data persistence. But before I did, I watched a couple of videos on MySQL and read the _MySQL Database_ section in w3schools. With ChatGPT's guidance, I opted to using PDO (PHP Data Objects) which is basically an API interface to communicate to a database, in this case MySQL. I used PDO because it has robust exception handling and it supports a wide range of databases unlike MySQLi. However I did learn MySQLi first before PDOs when I was learning for the first time MySQL. With PDOs, I converted my JSON file approach to a proper persistence logic.

If you haven't noticed earlier in the registration PHP logic code, the password is being saved directly without any encryption. PHP exposes a native `password_hash()` function which I used to hash the password before saving it.

During this time, the current login logic was:

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

Having built a solid authentication system, I moved on to the final part of development: Todos.

At first, things were handled in the dashboard page itself like creating a todo, fetching them, etc. But this complicates the dashboard page's code and is not very scalable. I therefore used a REST API approach where there are routes such as `actions/add_todo.php`. A RESTful approach might be `POST /api/todo`, `GET /api/todos`, etc. but I didn't bother because I don't want to bother setting up JavaScript's fetch for this for things like `PUT /api/todo` and `DELETE /api/todo`.

Here's an example of a server action:

```php
# actions/add_todo.php

# get the todos repository
$todoRepository = new TodoRepository($conn);

# check that the request method is POST and if it is, get the input payload and save to database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = trim($_POST["input"]);
  if (!empty($input)) {
    $todo = new CreateTodoDTO($user->id, $input);
    $todoRepository->insertOne($todo);
  }
}

# redirect back to dashboard
header("Location: /dashboard.php");
```

And that's pretty much how the logic works in this learning PHP project. I learned a lot of neat tricks as well which I wouldn't have learned otherwise if I didn't create a "big" learning project such as this.
