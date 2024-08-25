<?php

  function padSeqBoolPowerful ($n) {

    // https://www.geeksforgeeks.org/powerful-number/

    // First divide the  
    // number repeatedly by 2 
    while ($n % 2 == 0) 
    { 
        $power = 0; 
        while ($n % 2 == 0) 
        { 
            $n /= 2; 
            $power++; 
        } 
          
        // If only 2^1 divides  
        // n (not higher powers), 
        // then return false 
        if ($power == 1) 
        return false; 
    } 
  
    // if n is not a power of 2  
    // then this loop will execute 
    // repeat above process 
    for ($factor = 3;  
         $factor <= sqrt($n);  
         $factor += 2) 
    { 
        // Find highest power of  
        // "factor" that divides n 
        $power = 0; 
        while ($n % $factor == 0) 
        { 
            $n = $n / $factor; 
            $power++; 
        } 
  
        // If only factor^1 divides  
        // n (not higher powers), 
        // then return false 
        if ($power == 1) 
        return false; 
    } 
  
    // n must be 1 now if it is 
    // not a prime number. Since  
    // prime numbers are not powerful,  
    // we return false if n is not 1. 
    return ($n == 1); 

  }
  
?>