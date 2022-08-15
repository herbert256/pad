<?php

  if ( $padSeq_action_value === TRUE or ! ctype_digit($padSeq_action_value) )
    if ( $padSeqCnt )
      $padSeq_actionCnt = $padSeqCnt;
    else
      $padSeq_actionCnt = 1;
  else
    $padSeq_actionCnt = $padSeq_action_value;    

  if ( count($padSeq_result) > $padSeq_actionCnt )
    if ( $padSeq_action_name == 'first')
      return array_slice ( $padSeq_result, 0, $padSeq_actionCnt );
    else 
      return array_slice ( $padSeq_result, $padSeq_actionCnt * -1 );
  else
    return $padSeq_result;
  
?>