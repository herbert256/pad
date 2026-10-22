<?php

  // A returning visitor - the browser sends an old padReqID and padSesID of a directory below
  // the application next to the ones on the application's directory - asks a clean URL with
  // a ',' or a ';' in a directory segment, which a bracketed route takes: the page answers,
  // and the old pair is expired on each directory setcookie can write as a path, the one
  // with the ',' or ';' passed over. setcookie refused that path with an Error and the
  // request ended with a 500 for every visitor holding the cookies.

  $commaDir    = parse_url ( $padHost, PHP_URL_PATH ) . 'regression/clean_urls/';
  $commaResult = '';

  foreach ( [ 'e,n', 'en;x' ] as $commaSegment ) {

    $commaBody = @file_get_contents ( $padHost . "regression/clean_urls/index.php/$commaSegment/about", FALSE,
                   stream_context_create ( [ 'http' => [ 'header' => "Cookie: padReqID=DIRAAAAA; padSesID=SESAAAAA; padReqID=APPAAAAA; padSesID=SESBBBBB\r\n", 'ignore_errors' => TRUE ] ] ) );

    $commaResult .= $commaSegment . ': ' . preg_replace ( '/^HTTP\S* (\d+).*/', '$1', $http_response_header [0] ?? '' ) . ' - ' . trim ( strip_tags ( (string) $commaBody ) ) . ' - ';

    foreach ( $http_response_header ?? [] as $commaLine )
      if ( preg_match ( '/^Set-Cookie: (pad(?:Req|Ses)ID)=([^;]*);.*path=([^;]*)/i', $commaLine, $commaMatch ) )
        $commaResult .= $commaMatch [1] . ' ' . ( $commaMatch [2] === 'deleted' ? 'expired' : ( $commaMatch [1] == 'padSesID' ? $commaMatch [2] : 'new' ) )
                      . ' on ' . str_replace ( $commaDir, 'app/', $commaMatch [3] ) . ' | ';

  }

?>
