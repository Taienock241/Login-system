//php form handling notes 
//1. Form Handling in PHP
//Form handling is the process of collecting and processing user input from HTML forms using PHP.
//2. Form Submission Methods
//There are two main methods for submitting forms in PHP: GET and POST.
//- GET Method: The form data is appended to the URL as query parameters. It is suitable for retrieving data and should not be used for sensitive information.
//- POST Method: The form data is sent in the request body. It is suitable for submitting sensitive information and is the preferred method for most form submissions.
//3. Accessing Form Data
//In PHP, you can access form data using the superglobal arrays $_GET and $_POST, depending on the submission method used.
//- $_GET: Used to access data submitted via the GET method.
//- $_POST: Used to access data submitted via the POST method.
//4. Example of Form Handling
//HTML Form (form.html)
//<!DOCTYPE html>
//<html>
//<head>    
//    <title>Simple Form</title>
//</head>
//<body>
//    <form action="simpleform.php" method="post">
//        <label for="name">Name:</label>
//        <input type="text" id="name" name="name" required>
//        <br>
//        <label for="email">Email:</label>
//        <input type="email" id="email" name="email" required>
//        <br>
//        <input type="submit" value="Submit">
//    </form>
//</body>
//</html>
//PHP Form Handling Script (simpleform.php)
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    
    // Process the data (e.g., save to database, send email, etc.)
    echo "Name: " . $name . "<br>";
    echo "Email: " . $email;
} else {
    echo "Invalid request method.";
    echo "Invalid request method.";
}   
?>


//all superglobals in php
//1. $_GET: Used to collect data sent via the GET method.
//2. $_POST: Used to collect data sent via the POST method.
//3. $_REQUEST: Used to collect data sent via both GET and POST methods.
//4. $_SERVER: Contains information about the server and execution environment.
//5. $_SESSION: Used to store and retrieve session variables.
//6. $_COOKIE: Used to store and retrieve cookie values.
//7. $_FILES: Used to handle file uploads.
