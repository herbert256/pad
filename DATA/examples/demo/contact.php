<?php

  $title = 'Contact Form';
  $dataFile = DAT . 'demo/messages.json';
  $successMsg = '';
  $error = '';
  $errors = [];

  if ( ! is_dir ( DAT . 'demo' ) )
    @mkdir ( DAT . 'demo', 0755, TRUE );

  $formName    = $name    ?? '';
  $formEmail   = $email   ?? '';
  $formSubject = $subject ?? '';
  $formMessage = $message ?? '';

  if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'send' ) {

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

      $successMsg = 'Thank you for your message! We will get back to you soon.';

      $formName = $formEmail = $formSubject = $formMessage = '';
    }
    else
      $error = 'Please correct the errors below.';
  }

  $hasErrors = count ( $errors ) > 0;

?>