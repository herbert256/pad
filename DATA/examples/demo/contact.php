<?php

  $title    = 'Contact Form';
  $dataFile = 'demo/messages.json';   // under DATA/, for padFileGet and padFilePut

  // The rules of a valid message stand on the fields in contact.pad - rules='required|email' -
  // and are checked before this file runs: padPosted ( 'contact' ) is TRUE only for a post
  // that keeps them. A post that breaks one never gets here; the form comes back refilled
  // with what was typed, each message beside its field.

  // The rules let a field posted as a list - name[]=x - through, so a message whose fields
  // are not all text is not stored: htmlspecialchars() was handed the array and the post
  // ended on a 500. The form comes back as it was sent.

  $sent = padRequestOnly ( [ 'name', 'email', 'subject', 'message' ] );

  if ( padPosted ( 'contact' ) and count ( array_filter ( $sent, 'is_string' ) ) == count ( $sent ) ) {

    $messages = json_decode ( padFileGet ( $dataFile ), TRUE ) ?: [];

    $entry          = array_map ( 'htmlspecialchars', $sent );
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
