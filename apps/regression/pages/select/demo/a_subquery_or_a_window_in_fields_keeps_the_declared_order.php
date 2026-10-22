<?php

  // An aggregate in a subquery or over a window is a value of each row, no total: the select
  // keeps the order the table declares, and rows= the first page of it. Beside them a total
  // still drops that order - under ONLY_FULL_GROUP_BY, MySQL's default, it is an error there.

  db ( "set session sql_mode = concat(@@sql_mode, ',ONLY_FULL_GROUP_BY')" );

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name' ];

?>
