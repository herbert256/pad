<?php

  $title    = 'Guestbook';
  $dataFile = 'demo/guestbook.json';   // under DATA/, for padFileGet and padFilePut
  $error    = '';

  $entries = json_decode ( padFileGet ( $dataFile ), TRUE ) ?: [];

  if ( padRequestIs ( 'POST' ) and padRequest ( 'action' ) == 'add' ) {
    $name    = padRequest ( 'name',    '' );
    $comment = padRequest ( 'comment', '' );

    if ( padFilled ( $name ) and padFilled ( $comment ) ) {
      $entry = [
        'name'    => htmlspecialchars ( $name ),
        'comment' => htmlspecialchars ( $comment ),
        'date'    => padNow ( 'Y-m-d H:i:s' )
      ];
      array_unshift ( $entries, $entry );
      padFilePut ( $dataFile, json_encode ( $entries, JSON_PRETTY_PRINT ) );

      // Post, redirect, get: the browser is sent on to a plain GET of this page, so a
      // refresh shows the guestbook again instead of signing it a second time. The thanks
      // travel as a flash message, shown on that page once.

      padFlash ( 'Thank you for signing the guestbook!' );

      padRedirect ( 'guestbook' );
    }
    else
      $error = 'Please fill in both name and message.';
  }

  $hasEntries = count ( $entries ) > 0;

?>