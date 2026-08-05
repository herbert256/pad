<?php

  // The level loop: keeps calling level/level.php until the level this loop was entered at
  // has closed and $pad has dropped back below it.
  //
  // $padLevel is a stack of those floor levels, one entry per loop currently running, so a
  // nested pass started deeper in the tree stops when its own level closes instead of
  // unwinding the levels its caller is still inside. Entered once per run from
  // start/pad/go.php and once per nested pass from start/pad/pad.php.

  global $padLevel;

  $padLevel [] = $pad;

  while ( $pad >= end ( $padLevel ) )
    include PAD . 'level/level.php';

  array_pop ( $padLevel );

?>