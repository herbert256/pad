<?php

  // A read that does not start with SELECT - a WITH, a bracketed UNION, SHOW, DESCRIBE,
  // VALUES - answers its rows as an ARRAY does: no verb took it, so it ran and answered '',
  // its rows dropped without a word. A named query may start with WITH as well, where it
  // was refused as one that does not read.

  $with    = db ( "with t as (select name from staff where salary > {0}) select name from t order by name", [ 2000 ] );
  $union   = db ( "(select name from staff order by name limit 1) union (select 'x')" );
  $show    = db ( "show columns from staff" );
  $values  = db ( "values (1), (2)" );

  $with    = implode ( ' ', array_column ( $with, 'name' ) );
  $union   = implode ( ' ', array_column ( $union, 'name' ) );
  $show    = implode ( ' ', array_column ( $show, 'Field' ) );
  $values  = count ( $values );

  $minimum = 2000;

?>
