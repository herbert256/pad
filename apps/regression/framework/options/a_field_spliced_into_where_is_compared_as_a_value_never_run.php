<?php

  // A value - one a request could bring - spliced into the quoted expression of where= is a
  // string of it: it stood there bare, became part of the expression and called PHP. A
  // number stays a number; a value already in quotes stays in them.

  $f = '2 or $n eq php:abs(-3)';
  $k = 2;

?>
