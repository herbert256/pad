<?php

  function pad_seq ( $val, $min, &$wrk, &$seq, &$idx ) {

    $pad_wrk [$idx] = $val;

    if ( $val >= $min )
      $pad_seq [$idx] = 1;

    $idx++;

  } 

?>