<?php

  // Numeric text is a column position only as an item of the ORDER BY or GROUP BY list the
  // placeholder stands in, read from the statement's own clauses and brackets: a UNION's
  // select list after a GROUP BY is no such list ('007' stays text), an item after a
  // bracketed one is ('2' sorts on the salary), however long the list before it is.

  $union = db ( "array name, phone from staff group by name, phone union select {0}, {1}", [ 'x', '007' ] );
  $last  = end ( $union );

  $field = db ( "array name, salary from staff order by field(name, 'bob', 'jim'), {0} desc", [ '2' ] );

  $long  = db ( "array name, salary from staff order by " . str_repeat ( "1 * 0, ", 30 ) . "{0} desc", [ '2' ] );

  $phone   = $last ['phone'];
  $byField = implode ( ' ', array_column ( $field, 'name' ) );
  $byLong  = implode ( ' ', array_column ( $long,  'name' ) );

?>
