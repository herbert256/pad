<?php

  // {flush} sends the page above it at once: the first part of the body of flush/slow -
  // the part above the flush, and only that - arrives before the slow part below it is
  // rendered. Measured on the body, not on the first byte: PHP's own flush(), which a bare
  // {flush} used to call, sent the headers early and the whole body still at the end. The
  // response carries no Content-Length - it cannot be known before the whole page is - and
  // the page arrives whole.

  $flushStart = microtime ( TRUE );
  $flushFirst = NULL;
  $flushChunk = '';
  $flushBody  = '';
  $flushHead  = '';

  $flushCurl = curl_init ( $padGoExt . 'flush/slow&padInclude' );

  curl_setopt ( $flushCurl, CURLOPT_HEADERFUNCTION, function ( $curl, $line ) use ( &$flushHead ) {
    $flushHead .= $line;
    return strlen ( $line );
  } );

  curl_setopt ( $flushCurl, CURLOPT_WRITEFUNCTION, function ( $curl, $data ) use ( &$flushFirst, &$flushChunk, &$flushBody, $flushStart ) {
    if ( $flushFirst === NULL ) {
      $flushFirst = microtime ( TRUE ) - $flushStart;
      $flushChunk = $data;
    }
    $flushBody .= $data;
    return strlen ( $data );
  } );

  curl_exec ( $flushCurl );

  $flushTotal = microtime ( TRUE ) - $flushStart;

  echo 'early: ',  ( $flushTotal - $flushFirst > 0.2 and ! str_contains ( $flushChunk, 'slow part' ) ) ? 'yes' : 'no',
       ', length: ', stripos ( $flushHead, 'Content-Length:' ) !== FALSE ? 'sent' : 'none',
       ', body: ', trim ( $flushBody );

?>
