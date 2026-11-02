<?php

  // The database channel keeps a notification per recipient under DATA/notifications/, and
  // {widget:notifications/} lists the unread ones, newest first, each with a link that marks it
  // read. This request comes as the first one's link would bring it - its id and signature in
  // padNotificationRead - so the list shows only the second. The time is frozen, the
  // recipient's file emptied first.

  include_once PAD . 'subs/channels/_lib/store.php';

  @unlink ( padNotifyStoreFile ( 'reader-1' ) );

  padNowFreeze ( '2026-11-01 09:30:00' );

  padNotify ( [ 'id' => 'reader-1' ], 'shipped', [ 'order' => 41, 'items' => 1, 'name' => 'Ann', 'url' => 'https://shop.example/orders/41' ], [ 'database' ] );

  padNowFreeze ( '2026-11-01 10:15:00' );

  padNotify ( [ 'id' => 'reader-1' ], 'shipped', [ 'order' => 42, 'items' => 2, 'name' => 'Ann', 'url' => 'https://shop.example/orders/42' ], [ 'database' ] );

  $kept  = padNotifications ( 'reader-1' );
  $first = $kept [1] ['id'];

  $_GET ['padNotificationRead'] = $first . '.' . padNotifyStoreSign ( 'reader-1', $first );

?>
