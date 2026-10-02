<?php

  // last, last=N - keeps the trailing N entries, one when no count is given. A sequence
  // no longer than N is left untouched. Non-destructive: any stored sequence behind it
  // is unchanged, unlike pop.

  // The offset is counted from the front: -0 is 0, and slicing from 0 took every term, so
  // last=0 answered with all of them while first=0 answered with none.

  if ( count($pqResult) > $pqActionCnt )
    $pqResult = array_slice ( $pqResult, count ( $pqResult ) - $pqActionCnt );

?>