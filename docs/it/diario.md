# Il mio percorso nella creazione di un'applicazione web Todo in PHP

## Gli inizi

All'inizio, volevo solo imparare PHP. Quindi ho seguito tutto nel sito [PHP Tutorial](https://www.w3schools.com/php/), dalle basi fino alla sezione _MySQL Database_. (Avevo già iniziato il progetto dopo aver completato la sezione _PHP OOP_.)

Ma ovviamente, la teoria non resta impressa se non viene applicata. Quindi, il classico progetto web per applicare ciò che avevo imparato è stato un'app Todo perché coinvolge autenticazione, autorizzazione, operazioni CRUD e architettura dell'applicazione. Creare un'applicazione completamente funzionante da zero è stato il modo migliore per interiorizzare PHP e le sue sfumature.

Ho scelto di usare HTML puro, senza framework. Usare un framework mi avrebbe tolto la possibilità di progettare l'app web da zero e capire davvero come funziona PHP. Imparare sia un framework che PHP allo stesso tempo sarebbe stato controproducente. Inoltre, volevo fare le cose in modo hardcore.

Avendo esperienza pregressa con la struttura delle applicazioni, ho scelto la cosiddetta **"struttura piatta"** (_flat structure_ in inglese), dove moduli simili sono messi in cartelle proprie con un annidamento minimo. Per esempio:

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

Nota come i file sono piatti: senza annidamento profondo. Esistono anche altri approcci come la struttura **basata sulle funzionalità ovvero "feature"** (_feature-based structure_), dove `utils/`, `models/`, ecc., sono raggruppati dentro cartelle `feature-xxx/`. Ma per un'app semplice come questa, non è necessario.

> Le fonti per i pattern di quello che ho fatto per questo progetto, non ricordo più da dove ho preso né dovrei preoccuparmene. Sono esperienze che ho accumulato negli anni come appassionato di architettura, principi SOLID, design pattern, ecc.

Con la struttura delle cartelle pronta, era il momento di applicare alcuni pattern architetturali comuni per rendere il codice scalabile. Uno di questi è stata un'applicazione parziale della **Architettura Pulita** (_Clean Architecture_ in inglese). Noterai le cartelle `models/` e `repositories/`. I **modelli** contengono la logica/dati di business, e i **repository** gestiscono come interagire con quei modelli tramite il database. Ho anche usato il pattern **DTO (Data Transfer Object)** per definire cosa si aspetta ogni operazione in un repository. I repository, nel mio caso, agiscono anche come **servizi** che fungono da ponte tra l'applicazione e il database.

In una Clean Architecture completa, ci sarebbero anche i **"casi d'uso"** che fungono da ponte tra l'app web e i repository, e i repository sono il ponte verso il database vero e proprio. Perché preoccuparsi di questo livello di astrazione? Perché i repository sono specifici per un database: uno può usare MySQL, un altro MongoDB. Se astrai l'interazione tramite casi d'uso, puoi facilmente cambiare o aggiornare l'interazione con il database, anche a runtime!

> Sapevi che questa logica di sostituzione si chiama Strategy pattern? Se usi classi ed ereditarietà, puoi implementarlo con polimorfismo. Lo **Strategy pattern** prevede la creazione di più gestori logici e la scelta di uno a runtime. È in linea con il principio **Open-Closed** di SOLID: _il codice dovrebbe essere aperto all'estensione ma chiuso alla modifica_. Aggiungere una nuova strategia non dovrebbe richiedere modifiche al codice esistente. Anche il principio di **Inversione delle Dipendenze** (in inglese _Dependency Inversion Principle_ o **DIP**) è rilevante qui: _i moduli dovrebbero dipendere da astrazioni, non da implementazioni concrete_. Questo _"disaccoppia"_ le varie parti del codice. Lo Strategy pattern e la **Iniezzione delle Dipendenze** (in inglese _Dependency Injection_) sono due modi per raggiungere il DIP.

### Esempio di un Model

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

### Esempio di un Repository

```php
class UserRepository
{
  public function __construct(private PDO $conn) {}

  public function findById(int $id): ?User { /* ... */ }
  public function findByEmail(string $email): ?PasswordUser { /* ... */ }
  public function insertOne(CreateUserDTO $user): bool { /* ... */ }
}
```

Nota come il modello definisce cosa sono i dati, mentre il repository definisce come interagire con il database rispetto a questo modello. La bellezza del pattern repository è che l'applicazione web **non deve sapere nulla** sul database sottostante. Questo rende la scalabilità molto più semplice. Tuttavia, questi pattern possono portare a più astrazione e complessità. Più astrazione significa che è più difficile da tracciare, ma consente anche codice modulare ed estendibile. Questo si ricollega al principio Open-Closed e al principio di **Responsabilità Singola** (in inglese _Single Responsibility Principle_ o**SRP**).

Quando scrivevo questi model e repository, avevo la mentalità di TypeScript: li vedevo come interfacce. Per me, il modello `User` è come un'interfaccia `User`. (Non confonderla con le interfacce di Java.)

A quel tempo, non stavo ancora usando MySQL. Salvavo i dati usando file JSON. Ma il passaggio è stato facile perché tutto ciò che dovevo cambiare era la classe `UserRepository`. Non è fantastico?

## Implementazione di Registrazione e Login

La prima funzionalità che ho implementato è stata la registrazione e il login degli utenti. Ecco un estratto dalla pagina `sign-up.php`:

```php
<?php
  require "utils/sanitize.php";
  require "models/User.php";

  // Qui abbiamo un form vuoto
  $name = "";
  $email = "";
  $password = "";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filePath = __DIR__ . "/data/users.json";

    // Prendiamo gli utenti esistenti dal nostro database JSON
    // per controllare se un'email è già registrata
    $users = json_decode(file_get_contents($filePath), true) ?: [];

    // Poi controlliamo e sanitizziamo gli input dell'utente
    $name = sanitize($_POST["name"]);
    if (empty($name)) {
      $nameErr = "Il nome è obbligatorio";
    } elseif (!preg_match("/^[a-zA-Z-]*$/", $name)) {
      $nameErr = "Solo lettere e trattini sono ammessi";
    }

    $email = sanitize($_POST["email"]);
    if (empty($email)) {
      $emailErr = "L'email è obbligatoria";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Email non valida";
    } else {
      foreach ($users as $user) {
        if (isset($user["email"]) && $user["email"] === strtolower($email)) {
          $emailErr = "Email già registrata";
          break;
        }
      }
    }

    $password = sanitize($_POST["password"]);
    if (empty($password)) {
      $passwordErr = "La password è obbligatoria";
    } elseif (strlen($password) < 8) {
      $passwordErr = "La password deve essere lunga almeno 8 caratteri";
    } elseif (!preg_match("/^[a-zA-Z0-9-]*$/", $password)) {
      $passwordErr = "Solo lettere, numeri e trattini sono ammessi";
    }

    // E se tutto va bene, creiamo un nuovo utente e
    // lo reindirizziamo alla pagina di login (che è index.php)
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

Il login seguiva la stessa logica: prendere i dati dal JSON, validarli, reindirizzare alla dashboard. Ma per mantenere lo stato della sessione, ho usato i cookie:

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

Un **cookie** è un piccolo pezzo di dati inviato con ogni richiesta. Questo codice sopra era incluso nelle pagine che richiedevano il controllo degli accessi.

## Passaggio a MySQL con PDO

A questo punto, avevo registrazione/login e una semplice dashboard. Prima di passare a MySQL, l'ho studiato usando la sezione MySQL di W3Schools e alcuni video. Ho scelto di usare **PDO (PHP Data Objects)** invece di MySQLi perché:

- PDO supporta molti sistemi di database.
- Ha una gestione delle eccezioni migliore.

Avevo imparato prima MySQLi, poi sono passato a PDO. Convertire da JSON a database è stato semplice grazie all'astrazione del repository.

Le password inizialmente erano salvate in chiaro, una cattiva pratica. Quindi ho iniziato a usare `password_hash()`, funzione nativa di PHP, prima di salvare le password.

Ecco il vecchio codice del cookie per il login:

```php
# index.php

# se non ci sono errori di login, imposta un cookie che indica
# che l'utente ha effettuato l'accesso, contenente le sue credenziali
if (!isset($err)) {
  $cookieUser = ["name" => $user->name, "email" => $user->email];
  // scadenza del cookie delle credenziali in 1 settimana
  setcookie("credentials", json_encode($user), time() + 86400 * 7, "/");
  header("Location: /dashboard.php");
  exit;
}
```

Il che va bene, ma se un utente controlla i cookie del proprio browser, vedrà i dati in chiaro. Per evitare questo, molti siti usano i JWT. Un **JWT (JSON Web Token)** prende i dati e li firma con una chiave segreta. Questo non cripta i dati e quindi sono ancora visibili ma la firma alla fine assicura che i dati non siano stati manomessi. Quando il server riceve il token, ricalcola la firma e controlla se corrisponde. Se corrisponde, i dati sono considerati affidabili e l’utente è considerato valido. I JWT sono utili perché contengono essi stessi la sessione: il server **non ha bisogno** di salvare sessioni, né il client. Tuttavia, in questa web app PHP Todo, ho scelto un approccio basato su sessioni. (Non mi riferisco alle sessioni del browser o a `$_SESSION`, che vengono resettate alla chiusura del browser.)

In questo approccio, prima di impostare il cookie delle credenziali, esso viene criptato e salvato nel database. È necessario salvarlo perché, criptando il cookie, perdiamo la possibilità di decodificarlo. Quando un utente autenticato vuole accedere al sito, possiamo confrontare il suo cookie con quello salvato nel database, e se corrispondono allora è considerato valido:

```php
# index.php

# se non ci sono errori di login, imposta un cookie che indica
# che l'utente ha effettuato l'accesso, contenente le sue credenziali
if (!isset($err)) {
  # ora abbiamo un cookie repository che comunica con il nostro database
  $cookieRepository = new CookieRepository($conn);

  $cookieRaw = ["name" => $user->name, "email" => $user->email];
  $expiry = new DateTime("+7 days");

  # nota che ora criptiamo il cookie
  $token = password_hash(json_encode($cookieRaw), PASSWORD_BCRYPT);

  # poi lo salviamo
  $cookieDto = new CreateCookieDTO($user->id, $token, $expiry);
  $cookieRepository->insertOne($cookieDto);

  setcookie("credentials", $token, $expiry->getTimestamp(), "/");
  header("Location: /dashboard.php");
  exit;
}
```

## Aggiunta dei Todo

Con l'autenticazione in funzione, ho aggiunto i todo. Inizialmente, tutto era gestito dentro `dashboard.php`, ma non era scalabile. Quindi sono passato a un approccio simile a REST usando rotte di azione come `actions/add_todo.php`.

Un vero REST sarebbe `POST /api/todo`, `GET /api/todos`, ecc., ma non volevo gestire il `fetch()` di JavaScript.

Ecco un esempio:

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

## Conclusione

E così ho costruito il progetto. Creare un progetto di apprendimento "grande" come questo mi ha insegnato molti trucchi e sfumature di PHP che altrimenti non avrei mai scoperto.
