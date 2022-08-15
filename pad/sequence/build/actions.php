<?php
  
  foreach ( $padPrmsTag [$pad] as $padSeq_action_name => $padSeq_action_value )

    if ( $padSeq_action_name <> $padSeq_seq )

      if ( file_exists ( PAD . "sequence/actions/$padSeq_action_name.php" ) ) {

        if ( $padSeq_action_value === TRUE or ! ctype_digit($padSeq_action_value) )
          if ( $padSeqCnt )
            $padSeq_actionCnt = $padSeqCnt;
          else
            $padSeq_actionCnt = 1;
        else
          $padSeq_actionCnt = $padSeq_action_value;    

        $padSeq_result = include PAD . "sequence/actions/$padSeq_action_name.php";

        padDone ( $padSeq_action_name, TRUE );

      }
  
?>