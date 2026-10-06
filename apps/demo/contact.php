<?php

  $title    = 'Contact Form';
  $dataFile = 'demo/messages.json';   // under DATA/, for padFileGet and padFilePut

  // The rules of a valid message stand on the fields in contact.pad - rules='required|email' -
  // and are checked before this file runs: padPosted ( 'contact' ) is TRUE only for a post
  // that keeps them. A post that breaks one never gets here; the form comes back refilled
  // with what was typed, each message beside its field.

  if ( padPosted ( 'contact' ) ) {

    $messages = json_decode ( padFileGet ( $dataFile ), TRUE ) ?: [];

    $entry          = array_map ( 'htmlspecialchars', padRequestOnly ( [ 'name', 'email', 'subject', 'message' ] ) );
    $entry ['date'] = padNow ( 'Y-m-d H:i:s' );

    $messages [] = $entry;

    padFilePut ( $dataFile, json_encode ( $messages, JSON_PRETTY_PRINT ) );

    // Post, redirect, get: the browser is sent on to a plain GET of this page, so a refresh
    // shows the empty form instead of sending the message a second time - the thanks go
    // along as a flash message.

    padFlash ( 'Thank you for your message! We will get back to you soon.' );

    padRedirect ( 'contact' );

  }

?>
