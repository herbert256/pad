<?php

  // A value spliced into the quoted expression of where= or eval= is a string of it also when
  // a string of the other quote stands before it - '"': the quotes were counted, the odd one
  // made the value seem to stand in a string already, and it went in bare - the request value
  // 1 or php:gettype() eq 3 became part of the condition, true for every row, and of the
  // eval expression.

  $q  = '1 or php:gettype() eq 3';
  $g  = 'php:gettype()';
  $xs = [ [ 'n' => 1, 's' => 'a' ], [ 'n' => 3, 's' => 'b' ] ];

?>
