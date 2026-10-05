<?php

  padNowFreeze ( '2026-10-05 14:30:00' );

  padLog ( 'Order {order} paid by {customer}', 'info', [ 'order' => 1042, 'customer' => 'Ann' ] );

  $lines = file ( DATA . "logs/$padApp/2026-10-05.log", FILE_IGNORE_NEW_LINES );
  $line  = end ( $lines );

?>
