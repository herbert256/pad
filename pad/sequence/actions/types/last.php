<?php

  // last, last=N - keeps the trailing N entries, one when no count is given. A sequence
  // no longer than N is left untouched. Non-destructive: any stored sequence behind it
  // is unchanged, unlike pop.

  if ( count($pqResult) > $pqActionCnt )
    $pqResult = array_slice ( $pqResult, $pqActionCnt * -1 );

?>