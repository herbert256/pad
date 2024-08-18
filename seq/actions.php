<?php
  
  foreach ( $padPrm [$pad] as $padSeqActionName => $padSeqActionValue )

    if ( $padSeqActionName <> $padSeqSeq and file_exists ( "/pad/seq/actions/$padSeqActionName.php" ) ) {

      if ( $padSeqActionValue === TRUE or ! ctype_digit($padSeqActionValue) )
        if ( $padSeqCnt )
          $padSeqActionCnt = $padSeqCnt;
        else
          $padSeqActionCnt = 1;
      else
        $padSeqActionCnt = $padSeqActionValue;    

      if ( $GLOBALS ['padInfo'] ) 
        include '/pad/events/action.php';
 
      $padSeqResult = include "/pad/seq/actions/$padSeqActionName.php";

      padDone ( $padSeqActionName, TRUE );

    }
  
?>