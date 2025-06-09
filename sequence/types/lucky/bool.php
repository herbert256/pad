<?php

  function pqBoolLucky ($n, $p=0) {
    
    $counter = 2;
     
    if($counter > $n)
        return TRUE;

    $x =  (int) $n;

    if ( $x % $counter == 0 )
        return FALSE;
     
    $next_position = $n - ($n / $counter);
     
    $counter++;

    return pqBoolLucky ($next_position);

  }

?>