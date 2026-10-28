//how  define an array in php
//syntax
<!-- $arrayName = array(value1, value2, value3, ...); -->

//print an array in php
//syntax
<!-- print_r($arrayName); -->





//list all array methods in php
//1. array_change_key_case()
    //it changes the case of all keys in an array to either uppercase or lowercase.
    // It takes two parameters: the input array and the case to convert to (either CASE_UPPER or CASE_LOWER). 
    //It returns a new array with the keys converted to the specified case.    
//example:
<?php
$inputArray = array("first" => 1, "second" => 2, "third" => 3);
$upperCaseArray = array_change_key_case($inputArray, CASE_UPPER);
echo "Uppercase Array: ";
print_r($upperCaseArray); // Output: Array ( [FIRST] => 1 [SECOND] => 2 [THIRD] => 3 )
$lowerCaseArray = array_change_key_case($inputArray, CASE_LOWER);           

?>
   
   



//2. array_chunk()
  //it splits an array into chunks of a specified size.
  // It takes two parameters: the input array and the size of each chunk.
  //It returns a multidimensional array containing the chunks.
    //example:
<?php
$inputArray = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
$chunkedArray = array_chunk($inputArray, 3);
echo "Chunked Array: ";
print_r($chunkedArray); // Output: Array ( [0] => Array ( [0] => 1 [1] => 2 [2] => 3 ) [1] => Array ( [0] => 4 [1] => 5 [2] => 6 ) [2] => Array ( [0] => 7 [1] => 8 [2] => 9 ) )
?>


//3. array_column()
  //it returns the values from a single column in a multidimensional array.
  // It takes two parameters: the input array and the column key to extract.
    //It returns an array containing the values from the specified column.
        //example:
<?php
$inputArray = array(
    array("id" => 1, "name" => "John"),
    array("id" => 2, "name" => "Jane"),
    array("id" => 3, "name" => "Doe")
);
echo "Names Column: ";
$namesColumn = array_column($inputArray, "name");
print_r($namesColumn); // Output: Array ( [0] => John [1] => Jane [2] => Doe )
?>



//4. array_combine()
//5. array_count_values()
//6. array_diff()
//7. array_diff_assoc()
//8. array_diff_key()
//9. array_diff_uassoc()
//10. array_diff_ukey()
//11. array_fill()
//12. array_fill_keys()
//13. array_filter()
//14. array_flip()
//15. array_intersect()
//16. array_intersect_assoc()
//17. array_intersect_key()
//18. array_intersect_uassoc()
//19. array_intersect_ukey()
//20. array_key_exists()
//21. array_keys()
//22. array_map()
//23. array_merge()
//24. array_merge_recursive()
//25. array_multisort()
//26. array_pad()
//27. array_pop()
//28. array_product()
//29. array_push()
     //it adds one or more elements to the end of an array.
     // It takes two parameters: the input array and the values to be added.
     // It returns the new number of elements in the array after the push operation. 
       //example:
<?php
$inputArray = array(1, 2, 3);
$newCount = array_push($inputArray, 4, 5);
echo "New Count: " . $newCount . "\n"; // Output: New Count: 5
echo "Updated Array: ";
print_r($inputArray); // Output: Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 4 [4] => 5 )
?>


//30. array_rand()
//31. array_reduce()
   //it reduces an array to a single value using a callback function.   
   // It takes three parameters: the input array, the callback function, and an optional initial value.
   // The callback function should accept two parameters: the accumulator and the current value.
   // It returns the final reduced value after applying the callback function to all elements of the array  
      //example:
<?php
$inputArray = array(1, 2, 3, 4, 5);
$sum = array_reduce($inputArray, fn($accumulator, $currentValue) =>
    $accumulator + $currentValue, 0);
echo "Sum: " . $sum; // Output: Sum: 15
print_r($inputArray); // Output: Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 4 [4] => 5 ) 

?>

//32. array_replace()
//33. array_replace_recursive()

//34. array_reverse()
  //it reverses the order of the elements in an array.
  // It takes one parameter: the input array.
  // It returns a new array with the elements in reverse order.
    //example:
<?php
$inputArray = array(1, 2, 3, 4, 5);
$reversedArray = array_reverse($inputArray);
echo "Reversed Array: ";
print_r($reversedArray); // Output: Array ( [0] => 5 [1] => 4 [2] => 3 [3] => 2 [4] => 1 )
?>

//35. array_search()
//36. array_shift()
//37. array_slice()
//38. array_splice()
//39. array_sum()
//40. array_udiff()


//method to get array length in php
//syntax
<!-- count($arrayName); -->