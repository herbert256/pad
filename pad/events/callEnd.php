<?php

  // Fires from pad/call/_call.php and pad/call/_once.php right after the included PHP file
  // has run; adds the time since $padCallStart to the running total $padAppTime.
  //
  // pad/info/types/stats/end.php reports that total as 'call' and subtracts it from the
  // request time, so the 'usr' figure measures the engine itself and not the code it ran.

  global $padAppTime;

  if ( $padCall [0] == '/' )
    $padAppTime += hrtime ( TRUE ) - $padCallStart;

 ?>