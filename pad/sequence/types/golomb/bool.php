<?php

  // Membership test for the golomb sequence, 1, 2, 2, 3, 3, 4, 4, 4, ...: term n says how
  // often n occurs, so every whole number from 1 up occurs, a(n) times, and nothing else
  // does. build/check.php asks it for {keep golomb}, {remove golomb} and {flag golomb}.
  // Without it the check read the 10,000 terms of the PADgolomb table, whose largest is 356,
  // and generated the recurrence on to every larger candidate - a keep over 1..3000 made
  // three hundred thousand terms and ran into the time limit on a busy machine, answering
  // 713 members.
  //
  // Not the type's build: pqBuild() ranks order.php before bool.php.

  function pqBoolGolomb ( $n, $p=0 ) {

    return pqBoolWhole ( $n ) and $n >= 1;

  }

?>
