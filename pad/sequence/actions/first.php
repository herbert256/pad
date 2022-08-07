<?php

  if ( $pSeq_action_value === TRUE or ! ctype_digit($pSeq_action_value) )
    if ( $pSeqCnt )
      $pSeq_actionCnt = $pSeqCnt;
    else
      $pSeq_actionCnt = 1;
  else
    $pSeq_actionCnt = $pSeq_action_value;    

  if ( count($pSeq_result) > $pSeq_actionCnt )
    if ( $pSeq_action_name == 'first')
      return array_slice ( $pSeq_result, 0, $pSeq_actionCnt );
    else 
      return array_slice ( $pSeq_result, $pSeq_actionCnt * -1 );
  else
    return $pSeq_result;
  
?>