<?php
  
  foreach ( $padPrm [$pad] as $padSeqActionName => $padSeqActionValue )

    if ( $padSeqActionName <> $padSeqSeq and file_exists ( pad . "sequence/actions/$padSeqActionName.php" ) ) {

      if ( $padSeqActionValue === TRUE or ! ctype_digit($padSeqActionValue) )
        if ( $padSeqCnt )
          $padSeqActionCnt = $padSeqCnt;
        else
          $padSeqActionCnt = 1;
      else
        $padSeqActionCnt = $padSeqActionValue;    

      if ( padXapp ) include pad . 'info/types/xapp/events/action.php';
      if ( padXref ) include pad . 'info/types/xref/events/action.php';
 
      $padSeqResult = include pad . "sequence/actions/$padSeqActionName.php";

      padDone ( $padSeqActionName, TRUE );

    }
  
?>