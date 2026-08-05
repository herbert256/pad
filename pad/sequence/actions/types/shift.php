<?php

  // shift, shift=N - destructive: hands back the first N entries as the result and at the
  // same time removes them from the stored sequence named by $pqPull, setting
  // $pqStoreUpdated so exits/store/set.php writes the shortened store back. pop delegates
  // here and works from the other end. If the store holds no more than N it is emptied
  // while the whole result is still returned; with nothing pulled only the result is cut.

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