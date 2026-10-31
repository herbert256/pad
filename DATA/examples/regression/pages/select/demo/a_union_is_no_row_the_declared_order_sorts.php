<?php

  // A union makes rows of its own, as a group does: the declared order and the key are the
  // order of the table's rows, and after the union only its own columns can be named. A
  // select with union= - on the tag, declared, or a view of a table with an order - is
  // sorted by an order= written with it, or not at all. Over the staff and offices tables of
  // the demo database; sort= puts the answers in a fixed order.

  $padSelect ['staffSel']   = [ 'db' => 'staff',   'key' => 'name', 'order' => 'name' ];
  $padSelect ['cityPhones'] = [ 'db' => 'offices', 'fields' => 'phone' ];
  $padSelect ['phones']     = [ 'db' => 'staff',   'key' => 'name', 'fields' => 'phone', 'union' => 'cityPhones' ];
  $padSelect ['allPhones']  = [ 'base' => 'staffSel', 'fields' => 'phone', 'union' => 'cityPhones' ];

?>
