<?php

  $padSeqCheck = FALSE;
  $padSeqTry   = PHP_INT_MAX;
  $padSeqFixed = include "/pad/sequence/types/$padSeqSeq/fixed.php";
 
  include '/pad/sequence/build/oprToMain.php';

  foreach ( $padSeqFixed as $padSeqLoop )
    if ( ! include '/pad/sequence/build/one.php')
      break;

?>