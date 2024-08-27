<?php
  
  foreach ( $padSeqActions as $padSeqAction ) {

    extract ( $padSeqAction );

    if ( $padSeqActionValue === TRUE or ! ctype_digit($padSeqActionValue) )
      $padSeqActionCnt = 1;
    else
      $padSeqActionCnt = $padSeqActionValue;

    $padSeqResult = include "/pad/sequence/actions/types/$padSeqActionName.php";

  }
  
?>