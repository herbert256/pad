<?php

  // A select in the @else@ of a related select that found no row has no row to follow, so it
  // stands alone. It ended the page on "the relation to 'customerSel' is followed by its
  // field 'customerNumber', which the row of 'customerSel' does not hold - select it in
  // fields=". Over the Classic Models tables of the demo database.

  $padSelect ['customerSel'] = [ 'db' => 'customers', 'key' => 'customerNumber' ];
  $padSelect ['orderSel']    = [ 'db' => 'orders',    'key' => 'orderNumber' ];

  $padRelations ['orderSel'] ['customerSel'] = 'customerNumber';

?>
