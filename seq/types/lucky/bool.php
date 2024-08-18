<?php

  function padSeqBoolLucky ($n) {
    
    $counter = 2;
     
    if($counter > $n)
        return TRUE;

    $x =  (int) $n;

    if ( $x % $counter == 0 )
        return FALSE;
     
    $next_position = $n - ($n / $counter);
     
    $counter++;

    return padSeqBoolLucky ($next_position);

  }

?>