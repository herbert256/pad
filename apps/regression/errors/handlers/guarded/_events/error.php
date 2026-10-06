<?php

  // An error hook whose own failure is caught by one of the engine's try guards - an
  // expression that divides by zero - and reported from the guard's catch file.

  padEval ( 'php:intdiv(1, 0)' );

?>
