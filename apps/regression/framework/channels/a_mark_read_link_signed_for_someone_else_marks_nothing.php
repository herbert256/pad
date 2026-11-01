<?php

  // The mark-read link carries an HMAC of the application, the recipient and the id: one made
  // for another recipient - or by hand - marks nothing, and both notifications stay unread.

  include_once PAD . 'subs/channels/_lib/store.php';

  @unlink ( padNotifyStoreFile ( 'reader-2' ) );

  padNowFreeze ( '2026-11-01 09:30:00' );

  foreach ( [ 51, 52 ] as $order )
    padNotify ( [ 'id' => 'reader-2' ], 'shipped', [ 'order' => $order, 'items' => 1, 'name' => 'Bo', 'url' => '' ], [ 'database' ] );

  $id = padNotifications ( 'reader-2' ) [0] ['id'];

  $_GET ['padNotificationRead'] = $id . '.' . padNotifyStoreSign ( 'reader-3', $id );

?>
