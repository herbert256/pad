<?php

  // A pager over a declared select table: the page is cut by the SQL limit, so the rows do
  // not hold the total - the pager runs the count statement the select kept for it. The
  // links keep the request's other values; q stands in for a search field.

  $padSelect ['staffPaged'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name' ];

  $_GET ['q'] = 'j';
  $pg         = 2;

?>
