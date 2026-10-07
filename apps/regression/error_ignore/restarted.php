<?php

  // The restart page answers a bare 500 under this action - and so would a restart loop
  // that ran on until PHP's time limit, its stack or its memory ended it: the failure the
  // guard exists to prevent. A 500 within five seconds told the two apart only by chance -
  // a loop without the guard answered as fast on a smaller stack. The guard's own trace
  // does: every restart runs the request's start again, which sends the padReqID cookie
  // once more, and the guard stops it after twenty - twenty-one cookies, read off the
  // socket as they came.

  $url  = parse_url ( $padHost . $padApp . '/?restart&padInclude' );
  $sock = fsockopen ( $url ['host'], $url ['port'] ?? 80, $errno, $errstr, 10 );

  fwrite ( $sock, "GET {$url ['path']}?{$url ['query']} HTTP/1.1\r\nHost: {$url ['host']}\r\nConnection: close\r\n\r\n" );

  $head = explode ( "\r\n\r\n", stream_get_contents ( $sock ), 2 ) [0];

  fclose ( $sock );

  $verdict = ( preg_match ( '#^HTTP/\S+ 500#', $head ) and preg_match_all ( '/^Set-Cookie: padReqID=/mi', $head ) == 21 ) ? 'yes' : 'NO';

?>
