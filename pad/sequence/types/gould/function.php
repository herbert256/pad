<?php

  function pqGould ($n) {

    $x = $c = 1;

    for ($i = 1; $i <= $n; $i++) {

      $c = $c * ($n - $i) / $i;

      $c = (int) $c;
      
      if ($c % 2 == 1)
        $x++;
    
    }

    return $x; 

  }
  
?>