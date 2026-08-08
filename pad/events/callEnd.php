<?php

  // Fires from pad/call/_call.php and pad/call/_once.php right after the included PHP file
  // has run; adds the time since $padCallStart to the running total $padAppTime.
  //
  // pad/info/types/stats/end.php reports that total as 'call' and subtracts it from the
  // request time, so the 'usr' figure measures the engine itself and not the code it ran.

  // The start can be missing: the {trace} tag switches $padInfo on in the middle of exactly
  // such a call, so this end arrives for a call that began while the events were off. A call
  // that was not stamped is not charged.

  global $padAppTime;

  if ( $padCall [0] == '/' and isset ( $padCallStart ) )
    $padAppTime += hrtime ( TRUE ) - $padCallStart;

 ?>