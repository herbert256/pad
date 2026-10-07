<?php

  // A $name bound into where= that holds numeric text - a request value - is text on
  // MySQL: name = $zero with '0' compares the names with '0', where it was the number 0 and
  // every name that does not start with a digit matched. A number stays a number.

  $padSelect ['staffNum'] = [ 'db' => 'staff' ];

  $zero  = '0';
  $floor = '2500';

?>
