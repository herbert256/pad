<?php

  function padSeqBoolPronic($x, $p=0) {
 
    for ($i = 0;
         $i <= (sqrt($x));
         $i++)
 
        if ($x == $i * ($i + 1))
        return true;
 
    return false;

  }

?>