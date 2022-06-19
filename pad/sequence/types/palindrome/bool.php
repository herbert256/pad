<?php

  function pad_seq_now_bool_palindrome ($n) {

    if ( $n == pad_reverse($n) )
      return TRUE;
    else
      return FALSE;

  }

?>