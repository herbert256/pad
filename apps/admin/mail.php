<?php

  // The outbox of the 'file' mail transport - every mail padMail and {mail} sent while
  // $padMailTransport is 'file', the default: DATA/mail/<app>/*.eml. The list, newest
  // first, and one mail with its text, its HTML in a sandboxed frame, and its source.

  $title = 'Mail outbox';
  $app   = adminAppAsked ();
  $mail  = adminGet ( 'mail', adminField ( 'mail' ) );

  if ( ! preg_match ( '/^[A-Za-z0-9_-]+\.eml$/D', $mail ) or $app === '' or ! is_file ( DATA . "mail/$app/$mail" ) )
    $mail = '';

  if ( adminPost () ) {

    if ( adminField ( 'action' ) == 'delete' and $mail !== '' ) {
      adminRemove ( DATA . "mail/$app/$mail" );
      adminDone ( 'The mail is removed.', 'mail', [ 'app' => $app ] );
    }

    if ( adminField ( 'action' ) == 'delete-all' ) {
      $count = 0;
      foreach ( adminApps () as $name => $one )
        if ( $app === '' or $name === $app )
          foreach ( glob ( DATA . "mail/$name/*.eml" ) ?: [] as $file )
            $count += adminRemove ( $file );
      adminDone ( "$count mails removed.", 'mail', $app === '' ? [] : [ 'app' => $app ] );
    }

  }

  $mailRows = [];

  foreach ( adminApps () as $name => $one )
    if ( $app === '' or $name === $app )
      foreach ( glob ( DATA . "mail/$name/*.eml" ) ?: [] as $file ) {

        $headers = adminMailHeaders ( explode ( "\n\n", str_replace ( "\r\n", "\n", (string) file_get_contents ( $file, FALSE, NULL, 0, 8192 ) ), 2 ) [0] );

        $mailRows [] = [ 'app' => $name, 'mail' => basename ( $file ), 'time' => filemtime ( $file ),
                         'when' => adminWhen ( filemtime ( $file ) ), 'to' => $headers ['to'] ?? '',
                         'subject' => $headers ['subject'] ?? '', 'from' => $headers ['from'] ?? '',
                         'current' => basename ( $file ) === $mail ? 1 : 0 ];

      }

  usort ( $mailRows, fn ( $a, $b ) => $b ['time'] <=> $a ['time'] );

  $mailCount = count ( $mailRows );

  $viewing  = 0;
  $mailHead = [];

  if ( $mail !== '' ) {

    $viewing  = 1;
    $message  = adminMailRead ( DATA . "mail/$app/$mail" );
    $title    = $message ['headers'] ['subject'] ?? $mail;
    $mailText = $message ['text'];
    $mailHtml = $message ['html'];
    $mailRaw  = $message ['raw'];
    $hasHtml  = $mailHtml !== '' ? 1 : 0;

    foreach ( [ 'from', 'to', 'cc', 'bcc', 'reply-to', 'date', 'subject' ] as $name )
      if ( isset ( $message ['headers'] [$name] ) )
        $mailHead [] = [ 'name' => ucfirst ( $name ), 'value' => $message ['headers'] [$name] ];

  }

  $appRows = [];

  foreach ( adminApps () as $name => $one )
    if ( glob ( DATA . "mail/$name/*.eml" ) )
      $appRows [] = [ 'name' => $name, 'selected' => $name === $app ? 1 : 0 ];

?>
