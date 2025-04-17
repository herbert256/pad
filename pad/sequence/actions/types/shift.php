<?php

  if ( $pqPull )
    $pqStoreUpdated = TRUE;

  if ( $pqPull )
    if ( count($pqStore [$pqPull]) > $pqActionCnt )
      if ( $pqAction == 'shift')
        $pqStore [$pqPull] = array_slice($pqStore [$pqPull], $pqActionCnt);
      else
        $pqStore [$pqPull] = array_slice($pqStore [$pqPull], 0, $pqActionCnt * -1);
    else
      $pqStore [$pqPull] = [];

  if ( count($pqResult) > $pqActionCnt )
    if ( $pqAction == 'shift')
      return array_slice ( $pqResult, 0, $pqActionCnt );
    else 
      return array_slice ( $pqResult, $pqActionCnt * -1 );
  else
    return $pqResult;
  
?>