<?php

  // A union member declared with rows= - and the order that picks them - adds those rows
  // only, the best paid two of the staff beside the cities of the offices, whether the select
  // it is part of is paged or not. Both were left out without a word, and every row of the
  // member was added. Over the staff and offices tables of the demo database.

  $padSelect ['topStaff'] = [ 'db' => 'staff',   'fields' => 'name', 'order' => 'salary desc', 'rows' => 2 ];
  $padSelect ['names']    = [ 'db' => 'offices', 'fields' => 'city as name', 'union' => 'topStaff' ];

?>
