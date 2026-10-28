# PHP Backend Concepts Guide

This guide explains the PHP concepts used by this project and the broader concepts needed to build PHP backends. The examples are based on the login system in this folder:

- `db.php` connects to MySQL.
- `register.php` creates a user account.
- `login.php` authenticates a user.
- `dashboard.php` protects a page with a session.
- `logout.php` ends the session.
- `index.html` and `register.html` submit HTML forms to PHP scripts.

## 1. What PHP Does

PHP is a server-side programming language. A browser sends an HTTP request to a web server, the server runs PHP, and the server returns a response such as HTML, a redirect, JSON, or an error.

The browser does not receive the PHP source code. It receives only the result:

```php
<?php
echo "Hello";
?>
```

The browser receives:

```text
Hello
```

PHP can:

- receive and validate form data;
- read and write databases;
- authenticate users;
- manage sessions and cookies;
- generate HTML or JSON;
- call APIs and other services;
- read files and environment variables;
- enforce authorization rules.

PHP files usually use the `.php` extension. PHP code is placed inside `<?php ... ?>` tags. A closing `?>` tag is normally omitted in PHP-only files to prevent accidental whitespace from being sent to the browser.

## 2. Requests and Responses

An HTTP request contains a method, URL, headers, and sometimes a body. The most common methods are:

- `GET`: retrieve a resource; values commonly appear in the URL query string.
- `POST`: submit data or request an action; values commonly appear in the request body.
- `PUT` or `PATCH`: update data in APIs.
- `DELETE`: remove data in APIs.

The request method is available through `$_SERVER`:

```php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process submitted data.
} else {
    http_response_code(405);
    exit("Method not allowed");
}
```

The project uses this pattern in `login.php` and `register.php`. The forms specify `method="POST"` and send their data to the PHP file in the form's `action` attribute.

Useful response operations:

```php
http_response_code(400);
header("Content-Type: application/json");
echo json_encode(["error" => "Invalid input"]);
exit();
```

Headers must be sent before any output. A redirect is an HTTP response header:

```php
header("Location: dashboard.php");
exit();
```

Always call `exit()` after a redirect so PHP does not continue executing the rest of the script.

## 3. PHP Syntax and Values

PHP statements normally end with semicolons:

```php
$email = "user@example.com";
```

Variables begin with `$` and are dynamically typed:

```php
$name = "Alex";       // string
$age = 25;            // integer
$active = true;       // boolean
$items = ["a", "b"];  // array
$nothing = null;      // null
```

Prefer strict comparisons:

```php
if ($password !== $confirmPassword) {
    // Values must have the same type and value.
}
```

`==` performs type conversion and can produce surprising results. `===` compares both type and value. The same rule applies to `!=` versus `!==`.

Common operators include:

```php
$total = $price * $quantity;
$isValid = $email !== "" && $password !== "";
$label = $name ?? "Guest"; // Use "Guest" when $name is null or undefined.
```

Use `isset($value)` to check whether a variable exists and is not `null`. Use `empty($value)` carefully because values such as `0`, `"0"`, `false`, and `""` are considered empty.

## 4. Arrays and Associative Arrays

PHP arrays can act as lists or maps:

```php
$roles = ["user", "admin"];
echo $roles[0];

$user = [
    "id" => 7,
    "email" => "user@example.com",
];
echo $user["email"];
```

Superglobal request data is usually an associative array. For example:

```php
$email = $_POST["email"];
```

Do not assume a key exists. Read input safely:

```php
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
```

`$_POST`, `$_GET`, `$_SERVER`, `$_SESSION`, `$_COOKIE`, and `$_FILES` are called superglobals. They are available inside functions without being passed as parameters, but explicit parameters are usually easier to test.

## 5. Input Validation and Normalization

Input validation checks whether data is acceptable. Normalization puts equivalent input into a consistent form.

```php
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Enter a valid email address.");
}

if (strlen($password) < 8) {
    exit("Password must contain at least 8 characters.");
}
```

Validation is required even when HTML uses `required` or `type="email"`. Browser validation can be bypassed by sending a request directly.

Validate on the server:

- required fields;
- data type and length;
- email format;
- allowed values;
- password policy;
- authorization for the requested operation.

Do not remove or alter password whitespace unless that is an explicit product rule. Passwords should normally be passed to `password_hash()` exactly as entered.

## 6. Including Files: `require` and `include`

The project uses:

```php
require "db.php";
```

`require` loads and executes another PHP file. If it cannot load the file, PHP raises a fatal error. `include` also loads a file, but usually produces a warning and allows execution to continue when loading fails.

Use `require_once` for files that should be loaded only once:

```php
require_once __DIR__ . "/db.php";
```

`__DIR__` refers to the directory containing the current file and makes paths more reliable than depending on the current working directory.

Included files share the current scope. Keep shared files focused and avoid putting unexpected output in configuration files.

## 7. Functions, Scope, and Exceptions

Functions package reusable behavior:

```php
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
```

Type declarations make contracts clearer:

```php
function findUserByEmail(mysqli $conn, string $email): ?array
{
    // Return a user array or null.
}
```

Variables inside a function are local. Avoid global state where possible; pass dependencies such as a database connection into functions.

Exceptions represent failures that a caller can handle:

```php
try {
    // Operation that may fail.
} catch (Throwable $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit("An unexpected error occurred.");
}
```

Do not display database details, stack traces, or credentials to users in production. Log technical details privately and show a safe message.

## 8. MySQLi and Database Connections

`db.php` creates a MySQLi connection:

```php
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed");
}
```

The connection contains the information PHP needs to communicate with MySQL. A better production setup reads credentials from environment variables rather than committing them to source code:

```php
$conn = new mysqli(
    getenv("DB_HOST"),
    getenv("DB_USER"),
    getenv("DB_PASSWORD"),
    getenv("DB_NAME")
);

if ($conn->connect_error) {
    error_log($conn->connect_error);
    http_response_code(500);
    exit("Database unavailable.");
}

$conn->set_charset("utf8mb4");
```

`set_charset("utf8mb4")` ensures that text is exchanged using a Unicode-compatible character set.

The other common PHP database API is PDO. PDO supports prepared statements and a consistent interface for several database engines:

```php
$pdo = new PDO(
    "mysql:host=localhost;dbname=login_system;charset=utf8mb4",
    $user,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);
```

Use either MySQLi or PDO consistently in a project. Do not mix database APIs without a reason.

## 9. SQL Queries and Prepared Statements

The login script uses a prepared statement:

```php
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
```

The `?` is a placeholder. The value is sent separately from the SQL command. This prevents user input from becoming SQL syntax and is the primary defense against SQL injection.

MySQLi type characters include:

- `s`: string;
- `i`: integer;
- `d`: double/float;
- `b`: binary data.

Never build SQL by concatenating request data:

```php
// Unsafe:
$sql = "SELECT * FROM users WHERE email = '" . $_POST["email"] . "'";
```

Check that `prepare()` succeeds, use a unique database constraint for email addresses, and close statements and connections when appropriate. A database constraint is more reliable than only checking for duplicates in PHP because concurrent requests can race.

Typical table design:

```sql
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

## 10. Query Results

The project checks the number of matching rows and reads an associative row:

```php
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
}
```

`fetch_assoc()` returns a row keyed by column names. Always handle the cases of zero rows and unexpected multiple rows. The email `UNIQUE` constraint should ensure that a login lookup returns at most one user.

For queries that do not return rows, check the return value of `execute()` and inspect the error through server-side logging.

## 11. Password Hashing and Verification

Never store plaintext passwords. Registration correctly uses:

```php
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
```

`password_hash()` creates a salted, one-way password hash. `PASSWORD_DEFAULT` lets PHP select a secure default algorithm and may change in future PHP versions. The database column should be large enough for future hash lengths, such as `VARCHAR(255)`.

Login verifies the submitted password:

```php
if (password_verify($password, $user["password"])) {
    // The password matches.
}
```

Do not hash the login password again and compare strings. Password hashing includes a random salt, so a new hash will normally differ even for the same password.

For high-security applications, consider checking whether a stored hash needs an algorithm upgrade:

```php
if (password_needs_rehash($user["password"], PASSWORD_DEFAULT)) {
    // Rehash after a successful login and update the database.
}
```

Use generic login errors such as “Invalid email or password” to avoid revealing whether an account exists.

## 12. Sessions and Authentication

HTTP is stateless: each request is independent. PHP sessions provide server-side state associated with a session cookie.

Start a session before reading or writing `$_SESSION`:

```php
session_start();
```

The login script stores identity data:

```php
$_SESSION["user_id"] = $user["id"];
$_SESSION["email"] = $user["email"];
```

The dashboard checks authentication:

```php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit();
}
```

After successful authentication, regenerate the session ID to reduce session fixation risk:

```php
session_regenerate_id(true);
$_SESSION["user_id"] = $user["id"];
```

Logout clears session data:

```php
session_start();
$_SESSION = [];
session_destroy();
```

In production, configure cookies with `Secure`, `HttpOnly`, and an appropriate `SameSite` policy. `Secure` requires HTTPS, `HttpOnly` prevents JavaScript from reading the cookie, and `SameSite` helps reduce cross-site request attacks.

Authentication answers “Who is this user?” Authorization answers “What may this user do?” A session check alone is not enough for an administrative action; the server must also check the user's role or ownership.

## 13. Output Escaping and Cross-Site Scripting

The dashboard safely prints the session email:

```php
echo htmlspecialchars($_SESSION["email"]);
```

`htmlspecialchars()` converts characters such as `<`, `>`, `"`, and `&` so user-controlled text is treated as text instead of HTML or JavaScript.

Escape at the output context:

```php
echo htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
```

Do not assume data is safe because it came from your database. A database may contain text originally supplied by a user. Escape HTML output, use safe URL handling for links, and use JSON encoding when embedding data in JavaScript.

## 14. Redirects, Errors, and Status Codes

Use redirects after successful POST actions to prevent duplicate form submissions:

```php
header("Location: dashboard.php");
exit();
```

Useful status codes include:

- `200 OK`: successful response;
- `201 Created`: resource created;
- `302 Found`: temporary redirect;
- `400 Bad Request`: invalid input;
- `401 Unauthorized`: authentication is required or failed;
- `403 Forbidden`: authenticated but not allowed;
- `404 Not Found`: resource does not exist;
- `405 Method Not Allowed`: wrong HTTP method;
- `409 Conflict`: state conflict, such as a duplicate email;
- `500 Internal Server Error`: unexpected server failure.

Do not use `die()` for every error in a production application. A centralized error handler and consistent error pages or JSON responses are easier to maintain.

## 15. HTML Forms and PHP

The login form sends named controls:

```html
<form action="login.php" method="POST">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
```

Only controls with a `name` are included in submitted form data. PHP receives the values in `$_POST`.

The HTML `required`, `type`, and `pattern` attributes improve user experience but do not replace server-side validation. A client can disable browser checks or send an entirely custom HTTP request.

## 16. Cross-Site Request Forgery (CSRF)

CSRF occurs when another site causes a user's browser to submit an unwanted state-changing request while the user is authenticated.

For state-changing forms, generate a random token in the session:

```php
session_start();
$_SESSION["csrf_token"] ??= bin2hex(random_bytes(32));
```

Put it in the form:

```php
<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8") ?>"
>
```

Validate it in the receiving script:

```php
$token = $_POST["csrf_token"] ?? "";

if (!hash_equals($_SESSION["csrf_token"] ?? "", $token)) {
    http_response_code(403);
    exit("Invalid request.");
}
```

The current login and registration forms do not include CSRF tokens. Add them before using this application beyond local learning.

## 17. Authorization and Protected Routes

A protected page should perform its access check before output:

```php
function requireLogin(): void
{
    if (empty($_SESSION["user_id"])) {
        header("Location: index.html");
        exit();
    }
}
```

For role-based access:

```php
if (($_SESSION["role"] ?? "") !== "admin") {
    http_response_code(403);
    exit("Forbidden");
}
```

Never rely on hiding a button or link. The server must enforce every permission check.

## 18. Cookies

Sessions usually use a cookie containing a session identifier. Cookies can also store non-sensitive preferences:

```php
setcookie("theme", "dark", [
    "expires" => time() + 86400 * 30,
    "path" => "/",
    "secure" => true,
    "httponly" => true,
    "samesite" => "Lax",
]);
```

Do not put passwords, database credentials, or trusted authorization decisions directly in client-controlled cookies. Cookies can be viewed and modified by clients unless protected with a suitable server-side design.

## 19. Files, Uploads, and JSON APIs

Use `$_FILES` for uploads. Validate size, MIME type, file extension, and content; generate a server-side filename; store uploads outside the public web root when possible. Never trust the original filename.

For an API:

```php
header("Content-Type: application/json");
echo json_encode(["ok" => true]);
```

Read JSON request bodies with:

```php
$body = file_get_contents("php://input");
$data = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
```

Validate decoded data just as carefully as form data.

## 20. Object-Oriented PHP

Small scripts can be procedural, as in this project. Larger applications commonly use classes:

```php
final class UserRepository
{
    public function __construct(private mysqli $connection)
    {
    }

    public function existsByEmail(string $email): bool
    {
        // Query the database using a prepared statement.
        return false;
    }
}
```

Important object-oriented concepts include:

- classes and objects;
- properties and methods;
- constructors;
- visibility: `public`, `protected`, and `private`;
- interfaces and dependency injection;
- inheritance, used sparingly;
- namespaces to prevent name collisions.

A common backend separation is:

- controller: receives the request and builds the response;
- service: contains application rules;
- repository/model: communicates with the database;
- view/template: renders output.

## 21. Namespaces, Composer, and Autoloading

Namespaces organize code:

```php
namespace App\Auth;

final class LoginService
{
}
```

Composer manages PHP packages and can generate an autoloader. A typical project uses:

```bash
composer require vendor/package
```

Then:

```php
require __DIR__ . "/vendor/autoload.php";
```

Do not commit `vendor/` unless the project's deployment process specifically requires it. Commit `composer.json` and usually `composer.lock` for reproducible installations.

## 22. Configuration and Environment Variables

Development and production should not share hard-coded credentials. Use environment variables or a protected configuration mechanism:

```php
$debug = filter_var(getenv("APP_DEBUG"), FILTER_VALIDATE_BOOL);
$databaseName = getenv("DB_NAME");
```

Keep debug output disabled in production. Do not commit `.env` files containing real credentials. Add them to `.gitignore` and provide a safe example such as `.env.example`.

## 23. Logging and Debugging

Use logs for diagnostic information:

```php
error_log("Login failed for a request");
```

Do not log passwords, session IDs, access tokens, or full sensitive personal data. In production:

- log errors server-side;
- show generic messages to users;
- configure log rotation;
- monitor repeated authentication failures;
- avoid exposing PHP warnings and stack traces.

## 24. Testing PHP Backends

Test success and failure paths:

- valid registration;
- duplicate email;
- mismatched passwords;
- invalid email;
- short password;
- valid login;
- wrong password;
- unknown account;
- unauthenticated dashboard access;
- logout;
- malicious HTML in displayed fields;
- invalid HTTP methods.

Unit tests test individual functions or services. Integration tests test PHP, the database, sessions, and HTTP behavior together. Use a test database rather than a production database.

## 25. Improving This Learning Project

The current program demonstrates the core flow, but before production use it should also:

1. Validate and normalize server-side input.
2. Use generic login errors rather than revealing “User not found”.
3. Check all `prepare()` and query operations for failure.
4. Set the connection charset to `utf8mb4`.
5. Move database credentials to environment variables.
6. Add a `UNIQUE` constraint on `users.email`.
7. Call `session_regenerate_id(true)` after login.
8. Add CSRF tokens to state-changing forms.
9. Configure secure session cookies and require HTTPS.
10. Add password length and account security policies.
11. Use consistent status codes and user-friendly error handling.
12. Add rate limiting or lockout protection for repeated login failures.
13. Add a Content Security Policy and other security headers.
14. Use a template or response layer rather than mixing raw output and logic as the application grows.

## 26. A Safer Login Flow

A robust login request generally follows this order:

1. Start the session.
2. Require the expected HTTP method.
3. Read input using safe defaults.
4. Validate input.
5. Query with a prepared statement.
6. Use a generic failure response if the account does not exist.
7. Verify the password with `password_verify()`.
8. Regenerate the session ID.
9. Store only the minimum required identity data in the session.
10. Redirect and stop execution.

The corresponding logout flow is:

1. Start the session.
2. Clear session values.
3. Destroy the session.
4. Expire the session cookie when appropriate.
5. Redirect and stop execution.

The most important rule is that all security decisions must be made on the server. HTML, JavaScript, hidden fields, links, and cookies supplied by the client cannot be trusted as proof of identity or permission.
