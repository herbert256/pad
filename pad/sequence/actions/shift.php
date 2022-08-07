<?php

  if ( isset($pSequence_store [$pSeq_pull]) )
    if ( count($pSequence_store [$pSeq_pull]) > $pSeq_action_count )
      if ( $pSeq_action_name == 'shift')
        $pSequence_store [$pSeq_pull] = array_slice($pSequence_store [$pSeq_pull], $pSeq_action_count);
      else
        $pSequence_store [$pSeq_pull] = array_slice($pSequence_store [$pSeq_pull], 0, $pSeq_action_count * -1);
    else
      $pSequence_store [$pSeq_pull] = [];

  if ( count($pSeq_result) > $pSeq_action_count )
    if ( $pSeq_action_name == 'shift')
      return array_slice ( $pSeq_result, 0, $pSeq_action_count );
    else 
      return array_slice ( $pSeq_result, $pSeq_action_count * -1 );
  else
    return $pSeq_result;
  
?>