<?php 
require "db.php";
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $email = $_POST["email"];
    $password =$_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if($password !== $confirm_password){
        die("passwords do not match");
    }

    $hashed_password = password_hash($password,PASSWORD_DEFAULT);


    $sql = "SELECT id FROM users where email =?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        echo "<br>";
        echo "email already exist";
    }else{
        $sql = "INSERT INTO users (email,password)
        values(?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss",$email,$hashed_password);
        if($stmt->execute()){
            echo "Account created Successfully";
            echo '<br> <a href="index.html">Go to Login</a>';

        }else{
            echo "error creating Account";
        }

    }
    $stmt->close();
    $conn->close();
}else{
    die("Invalid request method");
}

?>