<?php

  if ( $padSeqPull )
    if ( count($padSeqStore [$padSeqPull]) > $padSeqActionCnt )
      if ( $padSeqActionName == 'shift')
        $padSeqStore [$padSeqPull] = array_slice($padSeqStore [$padSeqPull], $padSeqActionCnt);
      else
        $padSeqStore [$padSeqPull] = array_slice($padSeqStore [$padSeqPull], 0, $padSeqActionCnt * -1);
    else
      $padSeqStore [$padSeqPull] = [];

  if ( count($padSeqResult) > $padSeqActionCnt )
    if ( $padSeqActionName == 'shift')
      return array_slice ( $padSeqResult, 0, $padSeqActionCnt );
    else 
      return array_slice ( $padSeqResult, $padSeqActionCnt * -1 );
  else
    return $padSeqResult;
  
?>