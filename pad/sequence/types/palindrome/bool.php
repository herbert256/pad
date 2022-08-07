<?php

  function pSequence_bool_palindrome ($n) {

    if ( $n == pSeq_reverse($n) )
      return TRUE;
    else
      return FALSE;

  }

?>