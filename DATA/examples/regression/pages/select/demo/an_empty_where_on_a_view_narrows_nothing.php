<?php

  // An empty where= on the tag of a view narrows nothing: the view's own where holds, where
  // the empty one took its place and every row of the table came back. Over the four-row
  // staff table of the demo database.

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name' ];
  $padSelect ['paid']     = [ 'base' => 'staffSel', 'where' => 'salary > 1000' ];

?>
