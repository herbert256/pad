<?php

  function padSeqBoolSemiprime ($num) {

    $cnt = 0;
 
    for ( $i = 2; $cnt < 2 &&
          $i * $i <= $num; ++$i)
        while ($num % $i == 0) 
            $num /= $i;
             
               ++$cnt; 
 
    if ($num > 1)
        ++$cnt;

    return $cnt == 2;

  }

?>