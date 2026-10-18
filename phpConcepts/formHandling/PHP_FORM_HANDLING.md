# PHP Form Handling Concepts

This guide explains how PHP receives and processes HTML form submissions. It is based on [`simpleform.php`](./simpleform.php), which reads a submitted name and email address, checks the request method, escapes the values, and displays them.

## 1. What Form Handling Means

Form handling is the server-side process of:

1. displaying an HTML form;
2. receiving the submitted HTTP request;
3. reading the submitted values;
4. validating the values;
5. processing them, such as saving them to a database;
6. returning a response to the user.

The browser is controlled by the user, so PHP must validate every value on the server. HTML validation improves the user experience but is not a security boundary.

## 2. The HTML Form

A form uses the `form` element:

```html
<form action="simpleform.php" method="post">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <button type="submit">Submit</button>
</form>
```

Important attributes:

- `action`: the URL that receives the submission;
- `method`: the HTTP method, normally `get` or `post`;
- `name`: the key PHP uses to find the value;
- `id`: identifies the control for its label;
- `required`: browser-side required-field validation;
- `type`: describes the expected input, such as `email` or `password`.

Only successful controls with a `name` attribute are submitted. A disabled control is not submitted, and an unchecked checkbox is normally omitted.

## 3. GET and POST

### GET

With `method="get"`, data is placed in the URL:

```html
<form action="search.php" method="get">
    <input type="search" name="query">
    <button type="submit">Search</button>
</form>
```

For a search such as `php`, the request may look like:

```text
search.php?query=php
```

PHP reads this value from `$_GET`:

```php
$query = trim($_GET["query"] ?? "");
```

GET is appropriate for safe, repeatable retrieval operations such as searches, filters, and pagination. Do not use it for passwords or other sensitive values because URLs may be stored in browser history, server logs, analytics, and referrer headers.

### POST

With `method="post"`, data is sent in the request body:

```php
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
```

POST is normally used for creating or changing data, logging in, registering an account, and submitting sensitive form values. POST does not encrypt data by itself; HTTPS is still required.

## 4. Checking the Request Method

The example checks whether the request is a POST request:

```php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process the form.
} else {
    http_response_code(405);
    exit("Method not allowed.");
}
```

Use strict comparison (`===`) rather than loose comparison (`==`). A form endpoint should reject unexpected methods instead of processing an incomplete or incorrectly shaped request.

For a GET endpoint:

```php
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    exit("Method not allowed.");
}
```

## 5. Reading `$_POST` and `$_GET`

PHP exposes submitted values through superglobal associative arrays:

- `$_GET`: values submitted through the URL query string;
- `$_POST`: values submitted in a URL-encoded or multipart request body;
- `$_FILES`: uploaded file information;
- `$_SERVER`: request and server information.

Avoid reading a key without checking it exists:

```php
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
```

The `??` operator supplies an empty string when the key is missing. This avoids an undefined-key warning, but it does not validate the value.

Never trust the input type:

```php
$age = filter_input(INPUT_POST, "age", FILTER_VALIDATE_INT);

if ($age === false || $age === null) {
    exit("Age must be a valid integer.");
}
```

## 6. Validation and Normalization

Validation asks whether a value meets the application rules. Normalization converts acceptable values into a consistent form.

```php
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");

if ($name === "" || strlen($name) > 100) {
    http_response_code(422);
    exit("Name is required and must be 100 characters or fewer.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    exit("Enter a valid email address.");
}
```

Common validation rules include:

- required or optional;
- minimum and maximum length;
- email format;
- integer or decimal range;
- date format and allowed date range;
- allowed values from a fixed list;
- matching fields, such as password confirmation.

Do not use `htmlspecialchars()` as a replacement for validation. Escaping protects an output context; it does not prove that a value is a valid email, number, username, or date.

## 7. Output Escaping with `htmlspecialchars()`

The example uses:

```php
$name = htmlspecialchars($_POST["name"]);
echo "Name: " . $name;
```

The important security principle is to escape untrusted data when it is written into HTML:

```php
$name = trim($_POST["name"] ?? "");
echo htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
```

This helps prevent cross-site scripting (XSS). For example, input containing HTML or JavaScript is displayed as text instead of being interpreted as markup.

It is usually clearer to keep validation data separate from presentation data:

```php
$name = trim($_POST["name"] ?? "");

if ($name === "") {
    exit("Name is required.");
}

echo htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
```

Escape according to the output context:

- HTML text: `htmlspecialchars()`;
- HTML attribute: `htmlspecialchars()` with `ENT_QUOTES`;
- JavaScript data: `json_encode()`;
- SQL values: prepared statements;
- URLs: validate the scheme and encode URL components.

## 8. A Complete Basic POST Handler

```php
<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed.");
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$errors = [];

if ($name === "" || strlen($name) > 100) {
    $errors[] = "Name is required and must be 100 characters or fewer.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email address.";
}

if ($errors !== []) {
    http_response_code(422);
    foreach ($errors as $error) {
        echo "<p>" . htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8") . "</p>";
    }
    exit();
}

// Process the validated data here.
echo "Form submitted successfully.";
```

This example separates:

1. method checking;
2. input collection;
3. validation;
4. error response;
5. business processing;
6. output.

## 9. Returning a Response

PHP can return HTML, a redirect, or JSON.

### HTML response

```php
echo "<p>Saved successfully.</p>";
```

Escape every user-controlled value placed in the HTML.

### Redirect response

After a successful POST, use the Post/Redirect/Get pattern:

```php
header("Location: success.php");
exit();
```

This prevents the browser from resubmitting the form when the user refreshes the success page.

### JSON response

For an API endpoint:

```php
header("Content-Type: application/json; charset=utf-8");
echo json_encode(["success" => true]);
```

Set an appropriate status code before output:

```php
http_response_code(422);
echo json_encode(["errors" => ["Email is invalid."]]);
```

## 10. Common Form Controls

### Text and email fields

```php
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
```

### Password fields

```php
$password = $_POST["password"] ?? "";

if (strlen($password) < 8) {
    exit("Password must contain at least 8 characters.");
}
```

Never echo a password, log it, or store it as plaintext. Store passwords with `password_hash()` and verify them with `password_verify()`.

### Checkbox

An unchecked checkbox is usually absent:

```php
$termsAccepted = isset($_POST["terms"]) && $_POST["terms"] === "1";

if (!$termsAccepted) {
    exit("You must accept the terms.");
}
```

HTML:

```html
<input type="checkbox" name="terms" value="1">
```

### Radio buttons

```php
$role = $_POST["role"] ?? "";
$allowedRoles = ["student", "teacher"];

if (!in_array($role, $allowedRoles, true)) {
    exit("Invalid role.");
}
```

### Select fields

```php
$country = $_POST["country"] ?? "";
$allowedCountries = ["CA", "GB", "US"];

if (!in_array($country, $allowedCountries, true)) {
    exit("Invalid country.");
}
```

Never trust the value just because it came from an option that your page generated. A client can submit any value manually.

### Multiple values

For a group of checkboxes, use `[]` in the field name:

```html
<input type="checkbox" name="topics[]" value="php">
<input type="checkbox" name="topics[]" value="mysql">
```

Read and validate the array:

```php
$topics = $_POST["topics"] ?? [];

if (!is_array($topics)) {
    exit("Invalid topics.");
}

$allowedTopics = ["php", "mysql", "security"];
$topics = array_values(array_intersect($topics, $allowedTopics));
```

## 11. File Uploads

File forms require `enctype="multipart/form-data"`:

```html
<form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="avatar" accept="image/png,image/jpeg" required>
    <button type="submit">Upload</button>
</form>
```

PHP receives upload details in `$_FILES`:

```php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed.");
}

if (!isset($_FILES["avatar"]) || $_FILES["avatar"]["error"] !== UPLOAD_ERR_OK) {
    exit("Upload failed.");
}

if ($_FILES["avatar"]["size"] > 2_000_000) {
    exit("File is too large.");
}

$temporaryPath = $_FILES["avatar"]["tmp_name"];
$mimeType = mime_content_type($temporaryPath);
$allowedTypes = ["image/jpeg", "image/png"];

if (!in_array($mimeType, $allowedTypes, true)) {
    exit("Unsupported file type.");
}

$filename = bin2hex(random_bytes(16)) . ".bin";
move_uploaded_file($temporaryPath, __DIR__ . "/uploads/" . $filename);
```

For production uploads:

- enforce a size limit;
- validate the actual file content and MIME type;
- generate the filename on the server;
- do not trust the original filename or extension;
- store files outside the public web root when possible;
- prevent uploaded files from being executed as PHP;
- consider virus scanning.

## 12. CSRF Protection

Cross-site request forgery (CSRF) can cause a logged-in user's browser to submit an unwanted state-changing request. Protect authenticated POST forms with a session token.

Generate a token:

```php
session_start();

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
```

Put it in the form:

```php
<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8") ?>"
>
```

Validate it:

```php
$submittedToken = $_POST["csrf_token"] ?? "";

if (!hash_equals($_SESSION["csrf_token"] ?? "", $submittedToken)) {
    http_response_code(403);
    exit("Invalid request.");
}
```

Use secure session cookies and HTTPS as additional protections. CSRF tokens do not replace authentication or authorization.

## 13. Database Form Processing

When form data is saved to a database, use prepared statements:

```php
$stmt = $conn->prepare(
    "INSERT INTO contacts (name, email) VALUES (?, ?)"
);
$stmt->bind_param("ss", $name, $email);
$stmt->execute();
```

Do not concatenate form values into SQL:

```php
// Unsafe: vulnerable to SQL injection.
$sql = "INSERT INTO contacts (name) VALUES ('" . $_POST["name"] . "')";
```

Validation and output escaping do not prevent SQL injection. Prepared statements are the correct database protection.

## 14. Error Handling

Use status codes that describe the problem:

- `400 Bad Request`: malformed request;
- `405 Method Not Allowed`: incorrect HTTP method;
- `422 Unprocessable Content`: valid request format but invalid form values;
- `403 Forbidden`: request is not allowed;
- `500 Internal Server Error`: unexpected server failure.

Show users a safe error message and log technical details privately:

```php
try {
    // Database or other operation.
} catch (Throwable $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit("Unable to process the form right now.");
}
```

Do not expose SQL queries, file paths, stack traces, passwords, or credentials in a response.

## 15. Recommended Improvements to `simpleform.php`

The original example is useful for learning, but this version is safer and more explicit:

```php
<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed.");
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");

if ($name === "") {
    http_response_code(422);
    exit("Name is required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    exit("Enter a valid email address.");
}

echo "Name: " . htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8") . "<br>";
echo "Email: " . htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
```

The main differences are:

- strict request-method comparison;
- safe defaults when fields are missing;
- trimming input;
- server-side validation;
- a `422` response for invalid values;
- explicit UTF-8 output escaping;
- no processing of an unexpected request method.

## 16. Form Handling Checklist

Before deploying a form endpoint, verify that it:

- uses the correct `action` and `method`;
- checks the HTTP method on the server;
- reads values with safe defaults;
- validates all required fields and data types;
- limits lengths and sizes;
- uses prepared statements for database operations;
- escapes user-controlled output for its context;
- protects authenticated state-changing forms against CSRF;
- does not echo or log passwords;
- returns appropriate HTTP status codes;
- uses HTTPS for sensitive data;
- uses Post/Redirect/Get after successful POST requests;
- gives users safe messages while logging technical details privately;
- handles missing, duplicated, and unexpected fields.
