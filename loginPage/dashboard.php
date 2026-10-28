<?php 
       session_start();
       
       if(!isset($_SESSION["user_id"])){
        header("Location: index.html");
        exit();
       }


?>

<!DOCTYPE html>
<html>
    <head>

        <title>Dashboard</title>
    </head>
    <body>
        <h1>Welcome!</h1>
        <p>
            you are logged in as:
            <?php  echo htmlspecialchars($_SESSION["email"]);   ?>
        </p>
        <a href="logout.php">logout</a>
    </body>


</html>