<?php

  // Puts an action's survivors back into the sequence's original order after the action
  // reordered it - minimum and maximum sort by value to pick their extremes and then
  // come back through here. The picked entries move to $pqActionEnd and $pqResult is
  // rebuilt by walking the pre-action snapshot $pqActionStart, via keys.php when the
  // action kept the 'x' keys assoc.php applied, else via values.php.
  //
  // Returns $pqResult to the include, so callers write $pqResult = include ...

  $pqActionEnd = $pqResult;
  $pqResult    = [];

  foreach ( $pqActionEnd as $padK => $padV )
    if ( substr ( $padK, 0, 1 ) != 'x' )
      return include PQ . 'actions/order/values.php';

  return include PQ . 'actions/order/keys.php';

?>