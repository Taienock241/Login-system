//if elseif and else statements 
//syntax
<!-- if (condition) {
    //code to be executed if condition is true
} elseif (condition) {
    //code to be executed if the first condition is false and this condition is true
} else {
    //code to be executed if both conditions are false
} -->






$score = 85; // Example score

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Control Statements in PHP</h1>
    <?php if($score >= 90) : ?>
    <p>Excellent! You scored an A.</p>
<?php elseif($score >= 80) : ?>
    <p>Great job! You scored a B.</p>
<?php elseif($score >= 70) : ?>
    <p>Good effort! You scored a C.</p>
<?php elseif($score >= 60) : ?>
    <p>Keep trying! You scored a D.</p>
<?php else : ?>
    <p>Don't give up! You scored an F.</p>
<?php endif; ?>

</body>
</html>


//how torun the code
//1. Save the code in a file named controlstatements.php
//2. Open a terminal or command prompt
//3. Navigate to the directory where the file is saved
//4. Run the command: php controlstatements.php
//5. Open a web browser and go to http://localhost/controlstatements.php (if using a local server) or the appropriate URL if hosted on a web server