<?php

  if ( isset($padSequenceStore [$padSeqPull]) )
    if ( count($padSequenceStore [$padSeqPull]) > $padSeqActionCnt )
      if ( $padSeqActionName == 'shift')
        $padSequenceStore [$padSeqPull] = array_slice($padSequenceStore [$padSeqPull], $padSeqActionCnt);
      else
        $padSequenceStore [$padSeqPull] = array_slice($padSequenceStore [$padSeqPull], 0, $padSeqActionCnt * -1);
    else
      $padSequenceStore [$padSeqPull] = [];

  if ( count($padSeqResult) > $padSeqActionCnt )
    if ( $padSeqActionName == 'shift')
      return array_slice ( $padSeqResult, 0, $padSeqActionCnt );
    else 
      return array_slice ( $padSeqResult, $padSeqActionCnt * -1 );
  else
    return $padSeqResult;
  
?>