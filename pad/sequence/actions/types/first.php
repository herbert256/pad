<?php

  // first, first=N - keeps the leading N entries, one when no count is given. A sequence
  // no longer than N is left untouched. Non-destructive: any stored sequence behind it
  // is unchanged, unlike shift. A pull written as a single tag stores its result back under the
  // store's name whatever the action - exits/store/store.php - first as well.

  if ( count($pqResult) > $pqActionCnt )
    $pqResult = array_slice ( $pqResult, 0, $pqActionCnt );

?>
