<?php

  $title = 'Contact Form';
  $dataFile = DATA . 'demo/messages.json';
  $successMsg = isset ( $sent ) ? 'Thank you for your message! We will get back to you soon.' : '';
  $error = '';
  $errors = [];

  if ( ! is_dir ( DATA . 'demo' ) )
    @mkdir ( DATA . 'demo', 0755, TRUE );

  $formName    = $name    ?? '';
  $formEmail   = $email   ?? '';
  $formSubject = $subject ?? '';
  $formMessage = $message ?? '';

  if ( $_SERVER['REQUEST_METHOD'] == 'POST' && ( $action ?? '' ) == 'send' ) {

    $formName    = trim ( $formName );
    $formEmail   = trim ( $formEmail );
    $formSubject = trim ( $formSubject );
    $formMessage = trim ( $formMessage );

    if ( ! $formName )
      $errors [] = [ 'field' => 'Name is required' ];

    if ( ! $formEmail )
      $errors [] = [ 'field' => 'Email is required' ];
    elseif ( ! filter_var ( $formEmail, FILTER_VALIDATE_EMAIL ) )
      $errors [] = [ 'field' => 'Please enter a valid email address' ];

    if ( ! $formSubject )
      $errors [] = [ 'field' => 'Subject is required' ];

    if ( ! $formMessage )
      $errors [] = [ 'field' => 'Message is required' ];

    if ( empty ( $errors ) ) {

      $messages = [];
      if ( file_exists ( $dataFile ) ) {
        $json = file_get_contents ( $dataFile );
        $messages = json_decode ( $json, TRUE ) ?: [];
      }

      $messages [] = [
        'name'    => htmlspecialchars ( $formName ),
        'email'   => htmlspecialchars ( $formEmail ),
        'subject' => htmlspecialchars ( $formSubject ),
        'message' => htmlspecialchars ( $formMessage ),
        'date'    => date ( 'Y-m-d H:i:s' )
      ];

      file_put_contents ( $dataFile, json_encode ( $messages, JSON_PRETTY_PRINT ) );

      // Post, redirect, get: the browser is sent on to a plain GET of this page, so a
      // refresh shows the empty form instead of sending the message a second time. A
      // form with errors is answered in place, keeping what was typed.

      padRedirect ( 'contact', [ 'sent' => 1 ] );
    }
    else
      $error = 'Please correct the errors below.';
  }

  $hasErrors = count ( $errors ) > 0;

?>