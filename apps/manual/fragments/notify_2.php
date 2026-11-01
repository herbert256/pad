<?php

  // The database channel keeps the notification on the site. What Bo had before is marked
  // read first, so the list shows this one alone - Bo's id is one of this page's own, as the
  // example runs on two pages of the manual.

  $bo = [ 'id' => 'bo@' . $padPage ];

  padNotificationRead ( 'all', $bo );

  padNotify ( $bo, 'order-shipped', [ 'order' => 43, 'name' => 'Bo', 'url' => 'https://shop.example/orders/43' ], [ 'database' ] );

?>
