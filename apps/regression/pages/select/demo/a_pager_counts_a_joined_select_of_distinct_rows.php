<?php

  // The {pager} of a joined select whose select list decides what a row is - distinct, a
  // group - counts its rows, though the two tables share a column name: as a derived table
  // the select held customerNumber twice and the count ended on "Duplicate column name" -
  // with distinct(orders.customerNumber) too, which is a column named customerNumber.
  // Over the Classic Models tables of the demo database.

  $padSelect ['orderSel'] = [ 'db' => 'orders', 'key' => 'orderNumber' ];

?>
