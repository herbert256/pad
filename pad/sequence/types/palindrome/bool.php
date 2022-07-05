<?php

  function pad_sequence_bool_palindrome ($n) {

    if ( $n == pad_seq_reverse($n) )
      return TRUE;
    else
      return FALSE;

  }

?>