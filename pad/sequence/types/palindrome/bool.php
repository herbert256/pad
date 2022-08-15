<?php

  function padSequence_bool_palindrome ($n) {

    if ( $n == padSeq_reverse($n) )
      return TRUE;
    else
      return FALSE;

  }

?>