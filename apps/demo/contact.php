<?php

  $title    = 'Contact Form';
  $dataFile = DATA . 'demo/messages.json';
  $errors   = [];

  if ( ! is_dir ( DATA . 'demo' ) )
    @mkdir ( DATA . 'demo', 0755, TRUE );

  // The rules say what a valid message is; padValidate answers one message per field that
  // breaks them, and the {input} and {textarea} of contact.pad show each beside its field,
  // refilled with what was typed.

  if ( padPosted ( 'contact' ) ) {

    $errors = padValidate ( [
      'name'    => 'required|max:100',
      'email'   => 'required|email',
      'subject' => 'required|max:200',
      'message' => 'required|max:2000',
    ] );

    if ( ! $errors ) {

      $messages = [];
      if ( file_exists ( $dataFile ) ) {
        $json = file_get_contents ( $dataFile );
        $messages = json_decode ( $json, TRUE ) ?: [];
      }

      $messages [] = [
        'name'    => htmlspecialchars ( trim ( $_POST ['name']    ) ),
        'email'   => htmlspecialchars ( trim ( $_POST ['email']   ) ),
        'subject' => htmlspecialchars ( trim ( $_POST ['subject'] ) ),
        'message' => htmlspecialchars ( trim ( $_POST ['message'] ) ),
        'date'    => date ( 'Y-m-d H:i:s' )
      ];

      file_put_contents ( $dataFile, json_encode ( $messages, JSON_PRETTY_PRINT ) );

      // Post, redirect, get: the browser is sent on to a plain GET of this page, so a
      // refresh shows the empty form instead of sending the message a second time - the
      // thanks go along as a flash message. A form with errors is answered in place,
      // keeping what was typed.

      padFlash ( 'Thank you for your message! We will get back to you soon.' );

      padRedirect ( 'contact' );
    }

  }

  $hasErrors = count ( $errors ) > 0;

?>
