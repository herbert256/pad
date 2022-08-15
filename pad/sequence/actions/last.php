<?php

  if ( count($padSeq_result) > $padSeq_actionCnt )
    if ( $padSeq_action_name == 'first')
      return array_slice ( $padSeq_result, 0, $padSeq_actionCnt );
    else 
      return array_slice ( $padSeq_result, $padSeq_actionCnt * -1 );
  else
    return include 'first.php';
  
?>