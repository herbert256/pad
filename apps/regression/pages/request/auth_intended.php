<?php

  // A guest asks a page for the logged-in only (authhop) and is sent to the login page; the
  // login (authback) sends the visitor back to the page and the values first asked - the
  // engine's own values left out - and a second login, nothing kept any more, to the
  // default page. One cookie jar carries the session from request to request.

  $authJar = tempnam ( sys_get_temp_dir (), 'padAuthJar' );

  $authGet = function ( $page ) use ( $authJar, $padGoExt, $padHost, $padApp ) {
    $curl = padCurl ( [ 'url' => $padGoExt . $page, 'options' => [ 'FOLLOWLOCATION' => FALSE, 'COOKIEJAR' => $authJar, 'COOKIEFILE' => $authJar ] ] );
    return $curl ['result'] . ' ' . substr ( $curl ['info'] ['redirect_url'] ?? '', strlen ( $padHost . $padApp . '/' ) );
  };

  $authAsk   = $authGet ( 'request/authhop&sort=name&padFormat=json' );
  $authBack  = $authGet ( 'request/authback' );
  $authAgain = $authGet ( 'request/authback' );

  unlink ( $authJar );

?>
