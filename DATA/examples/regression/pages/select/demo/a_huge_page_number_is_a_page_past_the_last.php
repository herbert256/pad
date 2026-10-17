<?php

  // A page number from the request too high for an integer offset is a page past the last,
  // as page 1000 is: the page became PHP_INT_MAX, times the rows a float, and the statement
  // ended in "limit 1.844674407371E+19, 2" - an SQL error a visitor could raise at will.

  $padSelect ['officesPaged'] = [ 'db' => 'offices', 'key' => 'officeCode' ];

  $pg   = '99999999999999999999';
  $high = 1000;

?>
