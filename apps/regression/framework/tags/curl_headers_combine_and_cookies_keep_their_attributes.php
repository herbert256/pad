<?php

  // A crafted response through padCurlParse: a redirect's headers make way for the final
  // response's, a header sent twice keeps both values, and a cookie keeps its attributes.

  $raw = "HTTP/1.1 302 Found\r\nLocation: /x\r\nSet-Cookie: early=1; Path=/\r\n\r\n"
       . "HTTP/2 200\r\nvary: Accept\r\nVary: Cookie\r\nset-cookie: sid=abc=def; Path=/; HttpOnly\r\n\r\nbody";

  $out = padCurlParse ( [ 'url' => 'x', 'type' => '',
                          'info' => [ 'http_code' => 200, 'header_size' => strpos ( $raw, 'body' ) ] ], $raw );

  $curlCheck = ( $out ['headers'] ['vary'] ?? '' ) . '|'
             . ( $out ['cookies'] ['sid'] ?? '' ) . '|'
             . ( ( $out ['cookieAttributes'] ['sid'] ['httponly'] ?? FALSE ) ? 'httponly' : '' ) . '|'
             . ( isset ( $out ['headers'] ['Location'] ) ? 'redirect headers left' : 'final headers' );

?>
