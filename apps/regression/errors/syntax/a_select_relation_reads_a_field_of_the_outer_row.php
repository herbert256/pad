<?php

  // The relation is followed by the outer row's customerNumber, and the outer select's
  // fields= leaves that field out of its rows.

  $padSelect ['custRow']  = [ 'db' => 'customers', 'key' => 'customerNumber' ];
  $padSelect ['orderRow'] = [ 'db' => 'orders',    'key' => 'orderNumber' ];

  $padRelations ['orderRow'] ['custRow'] = [ 'customerNumber' => 'customerNumber' ];

?>
