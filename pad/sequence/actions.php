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
        include pad . 'tail/types/xref/items/action.php';

      $padSeqResult = include pad . "sequence/actions/$padSeqActionName.php";

      padDone ( $padSeqActionName, TRUE );

    }
  
?>