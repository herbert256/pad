<?php

  // The columns a declared join adds to fields= go on a line of their own when a -- comment
  // ends the tag's list: after it, on the same line, they were part of the comment and no
  // row held the customer. Over the Classic Models tables of the demo database.

  $padSelect ['customerName'] = [ 'db' => 'customers', 'key' => 'customerNumber', 'fields' => [ 'customerName' => 'customer' ] ];
  $padSelect ['orderSel']     = [ 'db' => 'orders', 'key' => 'orderNumber',
                                  'join' => [ 'inner' => 'customerName', 'key' => 'orders.customerNumber' ] ];

?>
