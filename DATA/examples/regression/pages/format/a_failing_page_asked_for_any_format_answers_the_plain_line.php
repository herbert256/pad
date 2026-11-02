<?php

  // A page that exposes its data and fails in its PHP (regression/errorpages/brokendata,
  // diagnostics off: a visitor's request), asked for its data in any format that writes - not
  // only JSON and CSV - answers the plain line as text/plain, as it skips the error page,
  // where ?brokendata&padFormat=ics, tsv or Accept: text/calendar got 25 lines of closing HTML
  // tags as text/html; asked for no data, the error page.

  $failAsk = function ( $query, $accept = '' ) use ( $padHost ) {
    $input  = [ 'url' => $padHost . "regression/errorpages/?$query" ];
    if ( $accept )
      $input ['headers'] = [ 'Accept' => $accept ];
    $answer = padCurl ( $input );
    $body   = trim ( $answer ['data'] );
    return "$query" . ( $accept ? " ($accept)" : '' ) . ': ' . $answer ['result'] . ' ' . explode ( ';', $answer ['headers'] ['Content-Type'] ?? '' ) [0] . ' '
         . ( preg_match ( '/^Error: \S+$/', $body ) ? 'the plain line' : ( str_contains ( $body, '</blockquote>' ) ? 'closing tags' : ( str_contains ( $body, 'error-page' ) ? 'the error page' : 'other' ) ) );
  };

  echo implode ( "\n", [
    $failAsk ( 'brokendata&padFormat=json' ),
    $failAsk ( 'brokendata&padFormat=ics' ),
    $failAsk ( 'brokendata&padFormat=tsv' ),
    $failAsk ( 'brokendata', 'text/calendar' ),
    $failAsk ( 'brokendata' )
  ] );

?>
