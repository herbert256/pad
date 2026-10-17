<?php

  // A view's declared where holds no value of the outer row, so a view nested in a table
  // it has no relation to - not even through its base - lists the same rows under every
  // row of it. The check missed it because the declared where made the where non-empty.

  $padSelect ['outerSel'] = [ 'db' => 'staff', 'key' => 'name' ];
  $padSelect ['richSel']  = [ 'db' => 'staff', 'key' => 'name', 'where' => 'salary > 2000' ];

?>
