<?php

  // Puts an action's survivors back into the sequence's original order after the action
  // reordered it - minimum and maximum sort by value to pick their extremes and then
  // come back through here. The picked entries move to $pqActionEnd and $pqResult is
  // rebuilt by walking the pre-action snapshot $pqActionStart through keys.php.
  //
  // By key always: asort and array_slice ( ..., TRUE ) keep every key, the integer keys an
  // earlier append, combine or randomize gave its values as well as the 'x' keys of
  // actions/assoc.php. A values.php walked the snapshot when one key was no 'x' key and
  // searched the selection for each value, so every repeat landed on the first entry holding
  // it - minimum=2 after an append of 1 to 5;1 answered 1, and the walk took time with the
  // square of the length.
  //
  // Returns $pqResult to the include, so callers write $pqResult = include ...

  $pqActionEnd = $pqResult;
  $pqResult    = [];

  return include PQ . 'actions/order/keys.php';

?>
