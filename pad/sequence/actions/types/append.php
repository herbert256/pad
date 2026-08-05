<?php

  // append='seq' - adds every value of each named stored sequence to the end of the
  // current one, keeping duplicates and renumbering. prepend does the same at the front.

  foreach ( $pqActionList as $pqAppendKey )
    foreach ($pqStore [$pqAppendKey] as $pqAppendValue)
      $pqResult [] = $pqAppendValue;

?>