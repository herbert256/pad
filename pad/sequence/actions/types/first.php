<?php

  // first, first=N - keeps the leading N entries, one when no count is given. A sequence
  // no longer than N is left untouched. Non-destructive: any stored sequence behind it
  // is unchanged, unlike shift.

  if ( count($pqResult) > $pqActionCnt )
    $pqResult = array_slice ( $pqResult, 0, $pqActionCnt );

?>