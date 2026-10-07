<?php

  // Numeric text is a number where MySQL wants one, as it is after LIMIT and OFFSET: a
  // column position after ORDER BY or GROUP BY, and the count of FETCH FIRST / NEXT. Quoted,
  // ORDER BY '2' sorted on a constant and FETCH NEXT '2' ROWS was a syntax error.

  $byPosition = db ( "array name, salary from staff order by {0} desc", [ '2' ] );
  $byLater    = db ( "array name, salary from staff order by 1 * 0, {0} desc", [ '2' ] );
  $fetched    = db ( "array name from staff order by name offset 1 rows fetch next {0} rows only", [ '2' ] );

  $order = implode ( ' ', array_column ( $byPosition, 'name' ) );
  $later = implode ( ' ', array_column ( $byLater,    'name' ) );
  $fetch = implode ( ' ', array_column ( $fetched,    'name' ) );

?>
