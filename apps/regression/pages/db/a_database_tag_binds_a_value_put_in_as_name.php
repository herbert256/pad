<?php

  // The SQL of a database tag is a quoted string, and a value goes into it as $name, bound
  // as an escaped literal of that variable - an array as a list, a row's field inside a
  // loop too. A value concatenated in was SQL, and one put in as {$name} reached the
  // database as PAD's inert stand-ins, so no tag had a way to take a value that worked.

  $who   = 'jim';
  $names = [ 'bob', 'joe' ];
  $odd   = "O'Br\\ien=a{b}";

?>
