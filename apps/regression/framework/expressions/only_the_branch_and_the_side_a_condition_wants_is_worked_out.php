<?php

  // Counts its calls, and sets $later, so a field read after it shows whether it was read
  // before the call or after.

  $branchCalls = 0;
  $later       = 'before';

  function branchCount ( $value = '' ) {
    $GLOBALS ['branchCalls']++;
    $GLOBALS ['later'] = 'after';
    return $value;
  }

?>
