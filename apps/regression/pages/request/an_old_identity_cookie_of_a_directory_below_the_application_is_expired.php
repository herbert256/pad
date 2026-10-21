<?php

  // A visitor who still holds the padReqID and padSesID a browser kept on a directory below
  // the application - from before they were set on the application's directory - gets those
  // expired, on the address's directory and each one between, and the session id goes on on
  // the application's directory: the old pair was sent first, the longer path ahead, PHP
  // kept the first of the two, and every later request of that directory reported one
  // frozen parent for the ten years the cookie lives.

  $oldDir  = parse_url ( $padHost, PHP_URL_PATH ) . 'regression/clean_urls/';
  $oldBody = @file_get_contents ( $padHost . 'regression/clean_urls/index.php/products/42', FALSE,
               stream_context_create ( [ 'http' => [ 'header' => "Cookie: padReqID=DIRAAAAA; padSesID=SESAAAAA\r\n", 'ignore_errors' => TRUE ] ] ) );

  $oldResult = '';

  foreach ( $http_response_header ?? [] as $oldLine )
    if ( preg_match ( '/^Set-Cookie: (pad(?:Req|Ses)ID)=([^;]*);.*path=([^;]*)/i', $oldLine, $oldMatch ) )
      $oldResult .= $oldMatch [1] . ' ' . ( $oldMatch [2] === 'deleted' ? 'expired' : ( $oldMatch [1] == 'padSesID' ? $oldMatch [2] : 'new' ) )
                  . ' on ' . str_replace ( $oldDir, 'app/', $oldMatch [3] ) . ' | ';

?>
