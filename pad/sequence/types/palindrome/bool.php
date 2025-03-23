<?php

  function padSeqBoolPalindrome ($n) {

    if ( $n == padTypeReverse($n) )
      return TRUE;
    else
      return FALSE;

  }

?>