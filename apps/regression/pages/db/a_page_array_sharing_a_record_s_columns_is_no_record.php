<?php

  // A heading list keyed by column name is the page's own array, not the row a RECORD read
  // before it answered: three occurrences, one per heading. The record stays one.

  $jim    = db ( "record name, phone from staff where name = 'jim'" );
  $labels = [ 'name' => 'Name', 'phone' => 'Phone', 'salary' => 'Salary' ];

?>
