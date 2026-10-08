<?php

  $orders = [ [ 'id' => 1, 'total' => 9.5, 'customer' => [ 'name' => 'Ada' ] ],
              [ 'id' => 2, 'total' => 12,  'customer' => [ 'name' => 'Bram' ], 'gift' => TRUE ] ];

  $types = padTypeScriptInterface ( 'OrdersVars', [ 'orders' => $orders, 'note' => NULL ] );

?>
