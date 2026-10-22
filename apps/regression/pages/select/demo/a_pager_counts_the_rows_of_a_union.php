<?php

  // A {pager} after a select with union= counts the rows of the whole union - four staff
  // and seven office cities - and not those of a select before it on the same level. Over
  // the staff and offices tables of the demo database.

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name' ];
  $padSelect ['allNames'] = [ 'db' => 'staff', 'fields' => 'name', 'union' => 'cities' ];
  $padSelect ['cities']   = [ 'db' => 'offices', 'fields' => 'city' ];

?>
