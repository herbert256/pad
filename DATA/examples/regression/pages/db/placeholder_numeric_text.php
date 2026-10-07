<?php

  // A value that looks like a number but arrives as text - a request value - is text to a
  // bare placeholder on MySQL: name = {0} with '0' compares the names with the text '0',
  // where MySQL took the number 0 and every name that does not start with a digit matched,
  // and '01234' keeps its leading zero. A number is still a number, and in a limit a
  // numeric text is the count it says.

  $zero    = db ( "field count(*) from staff where name = {0}",            [ '0' ] );
  $zip     = db ( "field {0}",                                             [ '01234' ] );
  $salary  = db ( "field count(*) from staff where salary > {0}",          [ '2500' ] );
  $number  = db ( "field count(*) from staff where name = {0}",            [ 0 ] );
  $limited = db ( "array name from staff order by name limit {0}",         [ '2' ] );
  $offset  = db ( "array name from staff order by name limit {0}, {1}",    [ '1', '2' ] );
  $names   = implode ( ' ', array_column ( $limited, 'name' ) ) . ' / '
           . implode ( ' ', array_column ( $offset,  'name' ) );

?>
