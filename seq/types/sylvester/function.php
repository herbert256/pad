<?php

  function padSeqSylvester ($n) {

    $N = 1000000007;
      
    // To store 
    // the product.
    $a = 1; 
      
    // To store the
    // current number.
    $ans = 2; 
  
    // Loop till n.
    for ($i = 1; $i <= $n; $i++)
    {

      if ($i==$n)
        return $ans;    
        $ans = (($a % $N) * ($ans % $N)) % $N;
        $a = $ans;
        $ans = ($ans + 1) % $N;
    }

    return $ans;

  }
  
      
?>