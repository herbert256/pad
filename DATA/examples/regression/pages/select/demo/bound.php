<?php

  // A value written into where= as $name is bound by the select as a quoted literal: a
  // quote in it stays inside the string and matches nothing, a comma is still a comma. In
  // order= a $name is spliced as column names. Over the customers table of the demo database.

  $padSelect ['bound'] = [ 'db' => 'customers' ];

  $who   = "x' or customerNumber > '0";
  $comma = 'Australian Collectors, Co.';
  $sort  = 'customerNumber desc';

?>
