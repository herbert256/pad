<?php

  $text   = pqArray ( 'repeat', "1, make, eval='php:abs(-123)'", 'rows=1' ) === [ "1, make, eval='php:abs(-123)'" ] ? 'kept as text' : 'ran';
  $comma  = pqArray ( 'repeat', 'a,b', 'rows=2' ) === [ 'a,b', 'a,b' ] ? 'comma kept' : 'comma changed';
  $amp    = pqArray ( 'repeat', 'a&b', 'rows=1' ) === [ 'a&b' ] ? 'ampersand kept' : 'ampersand escaped';
  $ints   = pqArray ( 'even', '', 'rows=3' ) === [ 2, 4, 6 ] ? 'integers' : 'strings';
  $listed = implode ( '|', pqArray ( 'prime', '', [ 'from' => 10, 'rows' => 3 ] ) );

?>
