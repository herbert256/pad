<?php

  // database - the notification kept on the site, for the person to read there: the
  // {widget:notifications/} tag lists the unread ones of the user logged in, each with a link that
  // marks it read; padNotifications and padNotificationRead do the same from PHP. A file per
  // recipient under DATA/notifications/<app>/ - no table to make, and it travels with the
  // offline applications. The recipient is its id, or its email when it has no id.

  return [
    'settings' => [ 'id' => [ 'to' => 'id|email' ] ],
    'send'     => function ( $message, $settings ) {
      include_once PAD . 'subs/channels/_lib/store.php';
      return padNotifyStoreAdd ( $settings ['id'], $message ) ? TRUE : 'the notification could not be kept under DATA/notifications/';
    },
  ];

?>
