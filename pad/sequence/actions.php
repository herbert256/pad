<?php
  
  foreach ( $padPrm [$pad] as $padSeqActionName => $padSeqActionValue )

    if ( $padSeqActionName <> $padSeqSeq and padExists ( pad . "sequence/actions/$padSeqActionName.php" ) ) {

      if ( $padSeqActionValue === TRUE or ! ctype_digit($padSeqActionValue) )
        if ( $padSeqCnt )
          $padSeqActionCnt = $padSeqCnt;
        else
          $padSeqActionCnt = 1;
      else
        $padSeqActionCnt = $padSeqActionValue;    

      if ( $padXref ) 
        padXref ( 'sequences', 'actions', $padSeqActionName );

      $padSeqResult = include pad . "sequence/actions/$padSeqActionName.php";

      padDone ( $padSeqActionName, TRUE );

    }
  
?>