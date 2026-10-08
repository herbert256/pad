<?php

  // The CORS fixture: _common off, the CSRF check on - a preflight is answered before it,
  // a cross-origin post still needs its token - and one origin allowed, with cookies, two
  // methods, two request headers, an exposed header and a preflight kept ten minutes. ?any
  // allows every origin, without cookies.

  $padCommon = FALSE;
  $padCsrf   = TRUE;

  $padCors = [ 'origins'     => [ 'https://app.example.com' ],
               'methods'     => [ 'GET', 'POST' ],
               'headers'     => [ 'Content-Type', 'X-Token' ],
               'credentials' => TRUE,
               'maxAge'      => 600,
               'expose'      => [ 'X-Total' ] ];

  if ( isset ( $_GET ['any'] ) )
    $padCors = [ 'origins' => '*' ];

?>
