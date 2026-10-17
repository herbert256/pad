<?php

  // A {data} named on its closing tag stores its content after the walk, its fields' values
  // in it, and a value standing bare among the elements of a list is refused: it ran as one
  // of their expressions - the request value php:strlen(12345) stored 5 - where the form
  // named on the opening tag keeps it text.

  $x = 'php:strlen(12345)';

?>
