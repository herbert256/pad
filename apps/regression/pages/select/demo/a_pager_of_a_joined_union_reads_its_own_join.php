<?php

  // The {pad:pager} of a select with a join and a union counts the rows by the select's own join:
  // the member's - none here - was read, and the count over a derived table of two columns
  // named name ended on "Duplicate column name". Four staff paired with themselves and their
  // four phones: eight rows, three pages of three.

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name' ];
  $padSelect ['phones']   = [ 'db' => 'staff', 'fields' => 'name, phone' ];

?>
