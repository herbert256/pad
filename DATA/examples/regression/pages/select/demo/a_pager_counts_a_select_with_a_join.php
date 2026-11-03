<?php

  // A pager after a select that joins two tables sharing a column name - orders and
  // customers, both with customerNumber - counts its rows: the count ran the select list
  // as a derived table, which MySQL refuses with two columns of one name ("1060: Duplicate
  // column name 'customerNumber'"), and the page ended at the pager.

  $padSelect ['ordersPaged'] = [ 'db' => 'orders', 'key' => 'orderNumber' ];

?>
