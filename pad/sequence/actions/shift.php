<?php

  if ( isset($padSequenceStore [$padSeq_pull]) )
    if ( count($padSequenceStore [$padSeq_pull]) > $padSeq_actionCnt )
      if ( $padSeq_action_name == 'shift')
        $padSequenceStore [$padSeq_pull] = array_slice($padSequenceStore [$padSeq_pull], $padSeq_actionCnt);
      else
        $padSequenceStore [$padSeq_pull] = array_slice($padSequenceStore [$padSeq_pull], 0, $padSeq_actionCnt * -1);
    else
      $padSequenceStore [$padSeq_pull] = [];

  if ( count($padSeq_result) > $padSeq_actionCnt )
    if ( $padSeq_action_name == 'shift')
      return array_slice ( $padSeq_result, 0, $padSeq_actionCnt );
    else 
      return array_slice ( $padSeq_result, $padSeq_actionCnt * -1 );
  else
    return $padSeq_result;
  
?>