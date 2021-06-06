<?php

  function pad_seq ( $val, $min, &$wrk, &$seq, &$idx ) {

    $wrk [$idx] = $val;

    if ( $val >= $min )
      $seq [$idx] = $val;

    $idx++;

  } 

?>