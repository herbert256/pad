<?php
  
  foreach ( $padPrmsTag [$pad] as $padSeqActionName => $padSeqActionValue )

    if ( $padSeqActionName <> $padSeqSeq )

      if ( file_exists ( PAD . "sequence/actions/$padSeqActionName.php" ) ) {

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