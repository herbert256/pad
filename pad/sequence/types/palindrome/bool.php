<?php

  function pad_sequence_palindrome ($n) {

    if ( $n == pad_reverse($n) )
      return TRUE;
    else
      return FALSE;

  }

?>