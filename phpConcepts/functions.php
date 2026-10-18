declare(strict_types=1); //forces strict type checking for function parameters and return types and it must be the first statement in the file before any other code or output.
//functions
//syntax
<!-- function functionName($parameter1, $parameter2) {
    //code to be executed
} -->
<?php  
     function addNumbers(int $num1, int $num2): int {
        return $num1 + $num2;
     }
     $result = addNumbers(5, 10);
     echo "The sum is: " . $result; // Output: The sum is: 15
 

    ?>



    //anonymous function
    <!-- $variable = function($parameter1, $parameter2) {
        //code to be executed
    }; -->
<?php 
  //anonymous function example
  $greet = function($name) {
    return "Hello, " . $name . "!";
  };
  echo $greet("John"); // Output: Hello, John!   


     ?>

    //arrow function
    <!-- $variable = fn($parameter1, $parameter2) => //code to be executed; --> 
//arrow function example
<?php
  $multiply = fn($a, $b) => $a * $b; //uses the fn keyword and the arrow (=>) syntax to define a function that takes two parameters and returns their product.
  echo $multiply(5, 10); // Output: 50

