<?php

  // shift, shift=N - destructive: hands back the first N entries as the result and at the
  // same time removes them from the stored sequence named by $pqPull, setting
  // $pqStoreUpdated so exits/store/set.php writes the shortened store back. pop delegates
  // here and works from the other end. If the store holds no more than N it is emptied
  // while the whole result is still returned; with nothing pulled only the result is cut.

  // Lengths and offsets are counted from the front, as for last: a pop of 0 multiplied out
  // to -0, which took every term and emptied the store.

  // A store that was never pushed has nothing to take out: the pull build has said so under
  // the strict check, and under the lenient walk the shift read the undefined key and ended
  // the request - only the result is cut then, as with nothing pulled.

  if ( $pqPull and isset ( $pqStore [$pqPull] ) )
    $pqStoreUpdated = TRUE;

  if ( $pqPull and isset ( $pqStore [$pqPull] ) )
    if ( count($pqStore [$pqPull]) > $pqActionCnt )
      if ( $pqAction == 'shift')
        $pqStore [$pqPull] = array_slice($pqStore [$pqPull], $pqActionCnt);
      else
        $pqStore [$pqPull] = array_slice($pqStore [$pqPull], 0, count ( $pqStore [$pqPull] ) - $pqActionCnt);
    else
      $pqStore [$pqPull] = [];

  if ( count($pqResult) > $pqActionCnt )
    if ( $pqAction == 'shift')
      $pqResult = array_slice ( $pqResult, 0, $pqActionCnt );
    else
      $pqResult = array_slice ( $pqResult, count ( $pqResult ) - $pqActionCnt );

?>
