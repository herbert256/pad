<?php

  function padSeqBoolLucky ($n) {
    $counter = 2;
     
    if($counter > $n)
        return 1;

    $x =  (int) $n;

    if ( $x % $counter == 0 )
        return 0;
     
    $next_position = $n - ($n / $counter);
     
    $counter++;

    return padSeqBoolLucky ($next_position);

  }

?>