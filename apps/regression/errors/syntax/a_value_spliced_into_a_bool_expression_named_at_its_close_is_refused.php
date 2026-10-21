<?php

  // A {bool} named on its closing tag evaluates its content after the walk, its fields'
  // values in it, and a value standing bare in the author's expression became part of it:
  // the request value php:strlen(12345) eq 5 or ran there and made the flag TRUE. Refused,
  // as the {data} list is; in quotes, '{$x}' eq 'Ann', it is a string of the expression.

  $x = 'php:strlen(12345) eq 5 or';

?>
