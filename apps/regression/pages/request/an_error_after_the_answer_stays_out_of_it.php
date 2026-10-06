<?php

  // An error raised after the response was sent wrote the error page - or the request id,
  // or the JSON report - behind the body, past the Content-Length the headers declared: a
  // client reusing the connection read those bytes as the start of its next response. The
  // page that fails after answering is read here off the socket, every byte of it.

  $url  = parse_url ( $padHost . 'regression/pages/?request/answered_then_failed&padInclude' );
  $sock = fsockopen ( $url ['host'], $url ['port'] ?? 80, $errno, $errstr, 10 );

  fwrite ( $sock, "GET {$url ['path']}?{$url ['query']} HTTP/1.1\r\nHost: {$url ['host']}\r\nConnection: close\r\n\r\n" );

  [ $head, $body ] = explode ( "\r\n\r\n", stream_get_contents ( $sock ), 2 ) + [ '', '' ];

  fclose ( $sock );

  preg_match ( '/^Content-Length:\s*(\d+)/mi', $head, $length );

  $endsThere = ( isset ( $length [1] ) and strlen ( $body ) == $length [1] ) ? 'yes' : 'NO';

?>
