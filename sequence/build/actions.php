<?php
  
  foreach ( $padPrm [$pad] as $padSeqActionName => $padSeqActionValue )

    if ( $padSeqActionName <> $padSeqSeq )

      if ( padExists ( PAD . "sequence/actions/$padSeqActionName.php" ) ) {

        if ( $padSeqActionValue === TRUE or ! ctype_digit($padSeqActionValue) )
          if ( $padSeqCnt )
            $padSeqActionCnt = $padSeqCnt;
          else
            $padSeqActionCnt = 1;
        else
          $padSeqActionCnt = $padSeqActionValue;    

        $padSeqResult = include PAD . "sequence/actions/$padSeqActionName.php";

        padDone ( $padSeqActionName, TRUE );

      }
  
?>