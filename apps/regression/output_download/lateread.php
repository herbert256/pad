<?php

  // A download ends where its Content-Length says: the writer sent the body and never said
  // it had (padSent) nor turned PHP's display of errors off, as the web writer does, so an
  // error after it - a shutdown function's warning, a late padError - was written behind
  // the declared length. The download is read off the socket.

  $url  = parse_url ( $padHost . 'regression/output_download/?late&padInclude' );
  $sock = fsockopen ( $url ['host'], $url ['port'] ?? 80, $errno, $errstr, 10 );

  fwrite ( $sock, "GET {$url ['path']}?{$url ['query']} HTTP/1.1\r\nHost: {$url ['host']}\r\nConnection: close\r\n\r\n" );

  [ $head, $body ] = explode ( "\r\n\r\n", stream_get_contents ( $sock ), 2 ) + [ '', '' ];

  fclose ( $sock );

  preg_match ( '/^Content-Length:\s*(\d+)/mi', $head, $length );

  $lateEnds = ( isset ( $length [1] ) and strlen ( $body ) == $length [1] ) ? 'yes' : 'NO';

?>
