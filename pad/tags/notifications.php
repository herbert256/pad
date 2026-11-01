<?php

  // {notifications/} - the notifications the database channel of padNotify kept for the user
  // logged in (lib/notify.php, subs/channels/), newest first, the unread ones: a list with each
  // one's title, text and time and a "Mark read" link back to this page, which marks it read
  // when the page is asked with it - the link carries a signature of the application, the
  // recipient and the id (padNotifyStoreSign), so it marks nothing of anyone else's. Nothing
  // for a guest. for= names the recipient instead of the user logged in - an id or an email;
  // all lists the read ones too.
  //
  //   {notifications/}
  //   {notifications}<li>{$title} - {$created} <a href="{$link}">done</a></li>{/notifications}
  //
  // As a pair the notifications are its rows: id, name, title, text, html, url, created (as
  // Y-m-d H:i), read (TRUE or FALSE) and link, the mark-read address - the handling options
  // apply to them as to any rows.

  include_once PAD . 'subs/channels/_lib/store.php';
  include_once PAD . 'subs/channels/_lib/list.php';

  $padNotifyKey  = padNotifyStoreKey ( padTagParm ( 'for', NULL ) );
  $padNotifyAll  = (bool) padTagParm ( 'all', FALSE );
  $padNotifyRows = [];

  if ( $padNotifyKey !== NULL ) {

    // The mark-read link's request: id.signature, the signature checked for this recipient.

    $padNotifyAsk = $_GET ['padNotificationRead'] ?? '';

    if ( is_string ( $padNotifyAsk ) and preg_match ( '/^([0-9a-f]{16}|all)\.([0-9a-f]{24})$/D', $padNotifyAsk, $padNotifyMatch )
         and hash_equals ( padNotifyStoreSign ( $padNotifyKey, $padNotifyMatch [1] ), $padNotifyMatch [2] ) )
      padNotifyStoreRead ( $padNotifyMatch [1], $padNotifyKey );

    $padNotifyPage = implode ( '/', array_map ( 'rawurlencode', explode ( '/', ( $padPageAsked ?? '' ) !== '' ? $padPageAsked : $padPage ) ) );

    foreach ( padNotifyStoreList ( $padNotifyKey, $padNotifyAll ) as $padNotifyOne )
      $padNotifyRows [] = [
        'id'      => (string) ( $padNotifyOne ['id'] ?? '' ),
        'name'    => (string) ( $padNotifyOne ['name'] ?? '' ),
        'title'   => (string) ( $padNotifyOne ['title'] ?? '' ),
        'text'    => (string) ( $padNotifyOne ['text'] ?? '' ),
        'html'    => (string) ( $padNotifyOne ['html'] ?? '' ),
        'url'     => (string) ( $padNotifyOne ['url'] ?? '' ),
        'created' => padNotifyWhen ( (int) ( $padNotifyOne ['created'] ?? 0 ), 'Y-m-d H:i' ),
        'stamp'   => padNotifyWhen ( (int) ( $padNotifyOne ['created'] ?? 0 ), 'c' ),
        'read'    => ! empty ( $padNotifyOne ['read'] ),
        'link'    => $padGo . $padNotifyPage . '&padNotificationRead=' . $padNotifyOne ['id'] . '.' . padNotifyStoreSign ( $padNotifyKey, $padNotifyOne ['id'] ) ];

  }

  if ( $padPair [$pad] )
    return $padNotifyRows;

  // A guest has no notifications - not even the line that there are none.

  if ( $padNotifyKey === NULL )
    return '';

  return padNotifyList ( $padNotifyRows );

?>
