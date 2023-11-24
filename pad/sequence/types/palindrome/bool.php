<?php

  function padSeqBoolPalindrome ($n) {

    if ( $n == padSeqReverse($n) )
      return TRUE;
    else
      return FALSE;

  }

?>