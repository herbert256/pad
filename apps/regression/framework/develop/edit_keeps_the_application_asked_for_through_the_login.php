<?php

  // A link into the editor names the application to open - the apps listing's edit links,
  // ?app=demo - and a visitor who is not logged in is sent to the login with it, so the
  // login sends them on to it; a name that is no application is not carried along.

  $where = function ( $asked ) use ( $padHost ) {
    $curl = padCurl ( $padHost . 'edit/?index&app=' . urlencode ( $asked ) );
    return $curl ['result'] . ' ' . substr ( (string) ( $curl ['info'] ['url'] ?? '' ), strlen ( $padHost ) );
  };

  $answer = 'demo: ' . $where ( 'demo' ) . ' / regression/main: ' . $where ( 'regression/main' ) . ' / nope: ' . $where ( 'nope' );

?>
