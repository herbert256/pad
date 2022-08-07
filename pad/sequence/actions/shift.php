<?php

  if ( isset($pSequenceStore [$pSeq_pull]) )
    if ( count($pSequenceStore [$pSeq_pull]) > $pSeq_actionCnt )
      if ( $pSeq_action_name == 'shift')
        $pSequenceStore [$pSeq_pull] = array_slice($pSequenceStore [$pSeq_pull], $pSeq_actionCnt);
      else
        $pSequenceStore [$pSeq_pull] = array_slice($pSequenceStore [$pSeq_pull], 0, $pSeq_actionCnt * -1);
    else
      $pSequenceStore [$pSeq_pull] = [];

  if ( count($pSeq_result) > $pSeq_actionCnt )
    if ( $pSeq_action_name == 'shift')
      return array_slice ( $pSeq_result, 0, $pSeq_actionCnt );
    else 
      return array_slice ( $pSeq_result, $pSeq_actionCnt * -1 );
  else
    return $pSeq_result;
  
?>