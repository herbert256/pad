<?php

  // A -- comment in a where ends with its own part of the where a view composes - with the
  // tag's where=, or with the where of the view it stands on - as it does in a where alone:
  // the parenthesis that closed the part stood after it, and the select ended on an SQL
  // syntax error. Over the four-row staff table of the demo database.

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name' ];
  $padSelect ['paid']     = [ 'base' => 'staffSel', 'where' => 'salary > 1000 -- the paid ones' ];
  $padSelect ['generous'] = [ 'base' => 'staffSel', 'where' => 'bonus > 100' ];
  $padSelect ['midPaid']  = [ 'base' => 'paid',     'where' => 'salary < 4000' ];

?>
