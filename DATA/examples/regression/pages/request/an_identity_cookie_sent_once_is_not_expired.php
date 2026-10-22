<?php

  // A padReqID and padSesID the browser sends once each are its only copies - there is no
  // old one of a directory below the application behind them - so a clean-URL request sends
  // no expiring Set-Cookie for them: only the new padReqID goes out, and padSesID not at
  // all, since it did not change. Every clean-URL request of every returning visitor got
  // two expiring lines per directory of the address and the session id again.

  $onceDir  = parse_url ( $padHost, PHP_URL_PATH ) . 'regression/clean_urls/';
  $onceBody = @file_get_contents ( $padHost . 'regression/clean_urls/index.php/products/42', FALSE,
                stream_context_create ( [ 'http' => [ 'header' => "Cookie: padReqID=APPAAAAA; padSesID=SESBBBBB\r\n", 'ignore_errors' => TRUE ] ] ) );

  $onceResult = '';

  foreach ( $http_response_header ?? [] as $onceLine )
    if ( preg_match ( '/^Set-Cookie: (pad(?:Req|Ses)ID)=([^;]*);.*path=([^;]*)/i', $onceLine, $onceMatch ) )
      $onceResult .= $onceMatch [1] . ' ' . ( $onceMatch [2] === 'deleted' ? 'expired' : ( $onceMatch [1] == 'padSesID' ? $onceMatch [2] : 'new' ) )
                   . ' on ' . str_replace ( $onceDir, 'app/', $onceMatch [3] ) . ' | ';

?>
