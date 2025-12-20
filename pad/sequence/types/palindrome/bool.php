<?php

  function pqBoolPalindrome ($n, $p=0) {

    if ( $n == padTypeReverse($n) )
      return TRUE;
    else
      return FALSE;

  }

?>
