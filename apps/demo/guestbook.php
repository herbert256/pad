<?php

  $title = 'Guestbook';
  $dataFile = DATA . 'demo/guestbook.json';
  $message = isset ( $signed ) ? 'Thank you for signing the guestbook!' : '';
  $error = '';

  if ( ! is_dir ( DATA . 'demo' ) )
    @mkdir ( DATA . 'demo', 0755, TRUE );

  $entries = [];
  if ( file_exists ( $dataFile ) ) {
    $json = file_get_contents ( $dataFile );
    $entries = json_decode ( $json, TRUE ) ?: [];
  }

  if ( $_SERVER['REQUEST_METHOD'] == 'POST' && ( $action ?? '' ) == 'add' ) {
    $name    = trim ( $name    ?? '' );
    $comment = trim ( $comment ?? '' );

    if ( $name && $comment ) {
      $entry = [
        'name'    => htmlspecialchars ( $name ),
        'comment' => htmlspecialchars ( $comment ),
        'date'    => date ( 'Y-m-d H:i:s' )
      ];
      array_unshift ( $entries, $entry );
      file_put_contents ( $dataFile, json_encode ( $entries, JSON_PRETTY_PRINT ) );

      // Post, redirect, get: the browser is sent on to a plain GET of this page, so a
      // refresh shows the guestbook again instead of signing it a second time.

      padRedirect ( 'guestbook', [ 'signed' => 1 ] );
    }
    else
      $error = 'Please fill in both name and message.';
  }

  $hasEntries = count ( $entries ) > 0;

?>