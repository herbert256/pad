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
      $pqResult = array_slice ( $pqResult, 0, $pqActionCnt );
    else
      $pqResult = array_slice ( $pqResult, $pqActionCnt * -1 );

?>
