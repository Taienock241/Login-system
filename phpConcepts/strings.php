//list all string methods in php
//1. strlen()
  //it returns the length of a string.
  // It takes a single parameter: the input string. 
  //It returns an integer representing the number of characters in the string.
    //example:
<?php
$string = "Hello, World!";
$length = strlen($string);
echo "Length of the string: " . $length; // Output: Length of the string: 13
?>



//2. str_word_count()
//3. strrev()
//4. strpos()
//5. str_replace()
//6. substr()
//7. strtoupper()
   //it converts all characters in a string to uppercase.   
   // It takes a single parameter: the input string.
   //It returns a new string with all characters converted to uppercase.
   //example:
<?php
$string = "Hello, World!";
$uppercaseString = strtoupper($string);
echo "Uppercase String: " . $uppercaseString; // Output: Uppercase String: HELLO, WORLD!
?>

//8. strtolower()
  //it converts all characters in a string to lowercase.    
  // It takes a single parameter: the input string.
  //It returns a new string with all characters converted to lowercase.
  //example:
<?php
$string = "Hello, World!";
$lowercaseString = strtolower($string);
echo "Lowercase String: " . $lowercaseString; // Output: Lowercase String: hello, world!
?>

//9. ucfirst()
//10. ucwords()
//11. trim()
//12. ltrim()
//13. rtrim()
//14. str_repeat()
//15. str_pad()
//16. str_split()
//17. str_shuffle()
//18. strcasecmp()
//19. strncasecmp()
//20. stristr()
//21. strrpos()
//22. strrchr()
//23. strspn()
//24. strcspn()
//25. strpbrk()
//26. strtr()
//27. strcoll()
//28. strnatcmp()
//29. strnatcasecmp()
//30. strftime()
//31. strptime()
//32. str_getcsv()
//34. str_ireplace()
//35. str_contains()
//36. str_starts_with()
//37. str_ends_with()
