<?php

  // Under this action an error is PHP's to show: once the answer had gone out, PHP's own
  // "Fatal error: Uncaught Exception" landed behind the body, past the Content-Length the
  // headers declared. The page that fails after answering is read off the socket.

  $url  = parse_url ( $padHost . 'regression/error_php/?answered_then_failed&padInclude' );
  $sock = fsockopen ( $url ['host'], $url ['port'] ?? 80, $errno, $errstr, 10 );

  fwrite ( $sock, "GET {$url ['path']}?{$url ['query']} HTTP/1.1\r\nHost: {$url ['host']}\r\nConnection: close\r\n\r\n" );

  [ $head, $body ] = explode ( "\r\n\r\n", stream_get_contents ( $sock ), 2 ) + [ '', '' ];

  fclose ( $sock );

  preg_match ( '/^Content-Length:\s*(\d+)/mi', $head, $length );

  $endsThere = ( isset ( $length [1] ) and strlen ( $body ) == $length [1] ) ? 'yes' : 'NO';

?>
