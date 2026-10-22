<?php

  // A select related to none of the selects around it is named with the one it stands in,
  // the nearest: the outermost was named, a select the inner one was never written in.

  $padSelect ['customerSel'] = [ 'db' => 'customers', 'key' => 'customerNumber' ];
  $padSelect ['orderSel']    = [ 'db' => 'orders',    'key' => 'orderNumber' ];
  $padSelect ['staffSel']    = [ 'db' => 'staff',     'key' => 'name' ];

  $padRelations ['orderSel'] ['customerSel'] = 'customerNumber';

?>
