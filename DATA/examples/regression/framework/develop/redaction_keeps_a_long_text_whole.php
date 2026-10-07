<?php

  // Every text a report, the toolbar or {debug} shows went through patterns that read an
  // assignment - $name = '...' - to find a secret's value in PHP source. A quoted literal of
  // ten thousand characters beside an = ran PCRE out of its stack: the text came back empty,
  // {debug} and the toolbar answered 500, a report showed null, the track copy 0 bytes.

  $long   = "\$a = '" . str_repeat ( 'x', 10000 ) . "';";
  $sql    = "UPDATE t SET a = 1 WHERE b IN ('a', '" . str_repeat ( 'y', 3000000 ) . "')";
  $result = ( padRedact ( $long ) === $long ? 'whole' : 'CUT' ) . ' ' . ( padRedact ( [ $sql ] ) [0] === $sql ? 'whole' : 'CUT' );

?>
