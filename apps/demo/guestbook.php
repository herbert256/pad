<?php

  $title = 'Guestbook';
  $dataFile = DAT . 'demo/guestbook.json';
  $message = '';
  $error = '';

  // Ensure data directory exists
  if ( ! is_dir ( DAT . 'demo' ) )
    @mkdir ( DAT . 'demo', 0755, TRUE );

  // Load existing entries
  $entries = [];
  if ( file_exists ( $dataFile ) ) {
    $json = file_get_contents ( $dataFile );
    $entries = json_decode ( $json, TRUE ) ?: [];
  }

  // Handle form submission
  if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'add' ) {
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
      $message = 'Thank you for signing the guestbook!';
    }
    else
      $error = 'Please fill in both name and message.';
  }

  $hasEntries = count ( $entries ) > 0;

?>
