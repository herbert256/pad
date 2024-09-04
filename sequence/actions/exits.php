<?php
  
  foreach ( $padSeqActions as $padSeqAction ) {

    extract ( $padSeqAction );

    if ( $padSeqActionList [0] === TRUE or ! ctype_digit($padSeqActionList [0]) )
      $padSeqActionCnt = 1;
    else
      $padSeqActionCnt = $padSeqActionList [0];

    $padSeqResult = include "/pad/sequence/actions/types/$padSeqActionName.php";

    $padSeqDone [] = $padSeqActionName;

  }
  
?>