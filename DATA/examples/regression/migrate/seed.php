<?php

  // padSeed runs _seeds/ in name order: the customers come from padFactory with fake data
  // seeded with 7, the same ten every run; the orders from plain db (), and the trigger of
  // a migration logs each. padSeed ( 'orders' ) runs only _seeds/02_orders.php.

  padMigrateFresh ( TRUE );

  $seeded    = implode ( ', ', padSeed () );
  $customers = db ( "array id, name, city, email from customers order by id" );
  $orders    = db ( "field count(*) from orders" );
  $logged    = db ( "array note, count(*) as n from order_log group by note order by note" );
  $one       = implode ( ', ', padSeed ( 'orders' ) );
  $orders2   = db ( "field count(*) from orders" );

?>
