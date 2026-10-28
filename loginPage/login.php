<?php   
     session_start();
     require "db.php";

     if($_SERVER["REQUEST_METHOD"] == "POST"){
      
     $email = $_POST["email"];
     $password = $_POST["password"];

     $sql = "SELECT * FROM users WHERE email = ?";
     
     $stmt = $conn->prepare($sql);
     $stmt ->bind_param("s",$email);

     $stmt->execute();

     $result = $stmt->get_result();

     if($result->num_rows == 1){
        $user = $result->fetch_assoc();

        if(password_verify($password,$user["password"])){
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["email"]  =  $user["email"];

            header("Location: dashboard.php");
            exit();
        } else{
            echo "incorrect password";
        }
     } else{
        echo "User not found";
     }

    $stmt->close();
    $conn->close();

     }else{
        die("invalid request method.");
     }


?>