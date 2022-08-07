<?php

  if ( count($pSeq_result) > $pSeq_actionCnt )
    if ( $pSeq_action_name == 'first')
      return array_slice ( $pSeq_result, 0, $pSeq_actionCnt );
    else 
      return array_slice ( $pSeq_result, $pSeq_actionCnt * -1 );
  else
    return include 'first.php';
  
?>