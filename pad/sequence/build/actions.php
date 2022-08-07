<?php
  
  foreach ( $pPrmsTag[$p] as $pSeq_action_name => $pSeq_action_value )

    if ( $pSeq_action_name <> $pSeq_seq )

      if ( file_exists ( PAD . "sequence/actions/$pSeq_action_name.php" ) ) {

        if ( $pSeq_action_value === TRUE or ! ctype_digit($pSeq_action_value) )
          if ( $pSeqCnt )
            $pSeq_actionCnt = $pSeqCnt;
          else
            $pSeq_actionCnt = 1;
        else
          $pSeq_actionCnt = $pSeq_action_value;    

        $pSeq_result = include PAD . "sequence/actions/$pSeq_action_name.php";

        pDone ( $pSeq_action_name, TRUE );

      }
  
?>