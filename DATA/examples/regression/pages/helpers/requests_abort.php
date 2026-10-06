<?php

  // padAbort answers the status with the message as plain text, or with the status's own
  // phrase - a status without a phrase of its own gets the phrase of its class - and
  // nothing the page made before the abort.

  $abortAsk = function ( $query ) use ( $padGoExt ) {
    $abort = padCurl ( $padGoExt . "helpers/requests_abort_go&padInclude$query" );
    return $abort ['result'] . ' ' . explode ( ';', $abort ['headers'] ['Content-Type'] ?? '' ) [0] . ' ' . trim ( $abort ['data'] );
  };

  // A status no registry knows - 499, 599 - is handed to the web server as it is, and the
  // server decides what goes out: php -S sends it, Apache sends 500. Only the body is
  // padAbort's own there, so only the body is compared.

  $abortBody = fn ( $query ) => trim ( substr ( $abortAsk ( $query ), 4 ) );

  $abortResult = implode ( ' | ', [
    $abortAsk ( '' ),
    $abortAsk ( '&code=403' ),
    $abortAsk ( '&code=429&message=Slow+down,+please' ),
    $abortAsk ( '&code=503&message=Back+at+six' ),
    $abortBody ( '&code=499' ),
    $abortBody ( '&code=599' ),
  ] );

?>
