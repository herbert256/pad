<?php

  // Cross-origin requests: which other sites' scripts may call this application from the
  // browser - a front end on app.example.com fetching the JSON of api.example.com.
  //
  //   $padCors = [ 'origins'     => [ 'https://app.example.com' ],   // or [ '*' ]
  //                'methods'     => [ 'GET', 'POST' ],               // default: the one asked
  //                'headers'     => [ 'Content-Type' ],              // default: the ones asked
  //                'credentials' => FALSE,                           // cookies along
  //                'maxAge'      => 600,                             // a preflight kept, seconds
  //                'expose'      => [ 'X-Total' ] ];                 // headers a script may read
  //
  // A request whose Origin header is listed - or any, with '*' - gets
  // Access-Control-Allow-Origin: the origin itself, with Vary: Origin, or * for '*' without
  // credentials (a browser refuses * with them, so the origin is named then), and
  // Access-Control-Allow-Credentials and -Expose-Headers when set. A preflight - OPTIONS
  // with an Access-Control-Request-Method - is answered 204 at once with -Allow-Methods,
  // -Allow-Headers and -Max-Age: no page renders, no CSRF token is asked, none of the
  // application runs. A request from an origin that is not listed gets no CORS header at
  // all, and its browser keeps the answer from the script; a preflight from one is still
  // answered 204, without them. With $padCors empty, the default, nothing of this happens.
  //
  // CORS is the browser's rule, not the server's protection: a request from another
  // origin still runs, and $padCsrf is what turns away a cross-site post.
  //
  // padCors        the headers for this request, a preflight answered
  // padCorsConfig  the setting checked and filled in with the defaults
  // padCorsList    a list setting as a list of trimmed texts
  // padCorsOrigin  the Origin header, when it is a well-formed origin

  function padCors () {

    global $padCorsVary;

    $cors = padCorsConfig ( $GLOBALS ['padCors'] ?? [] );

    if ( ! $cors or ! $cors ['origins'] )
      return;

    $any    = in_array ( '*', $cors ['origins'], TRUE );
    $origin = padCorsOrigin ();

    $allowed = ( $origin !== '' and ( $any or in_array ( strtolower ( $origin ), $cors ['origins'], TRUE ) ) );

    $preflight = ( ( $_SERVER ['REQUEST_METHOD'] ?? '' ) === 'OPTIONS'
                   and isset ( $_SERVER ['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ) );

    if ( headers_sent () )
      return;

    // Whether the answer names the origin decides whether it differs per origin, and a
    // cache along the way has to know - on every answer, also one that names none
    // (lib/output.php keeps Origin in the Vary it writes).

    if ( ! $any or $cors ['credentials'] ) {
      $padCorsVary = TRUE;
      header ( 'Vary: Origin', FALSE );
    }

    if ( $allowed ) {

      header ( 'Access-Control-Allow-Origin: ' . ( ( $any and ! $cors ['credentials'] ) ? '*' : $origin ) );

      if ( $cors ['credentials'] )
        header ( 'Access-Control-Allow-Credentials: true' );

      if ( $cors ['expose'] and ! $preflight )
        header ( 'Access-Control-Expose-Headers: ' . implode ( ', ', $cors ['expose'] ) );

    }

    if ( ! $preflight )
      return;

    if ( $allowed ) {

      $method = strtoupper ( trim ( (string) $_SERVER ['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ) );
      $asked  = (string) ( $_SERVER ['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? '' );

      $methods = $cors ['methods'] ?: ( preg_match ( '/^[A-Z]{1,20}$/', $method ) ? [ $method ] : [] );
      $headers = $cors ['headers'] ?: padCorsList ( preg_match ( '/^[A-Za-z0-9_, -]{0,1000}$/D', $asked ) ? $asked : '' );

      if ( in_array ( '*', $methods, TRUE ) and preg_match ( '/^[A-Z]{1,20}$/', $method ) )
        $methods = [ $method ];

      if ( in_array ( '*', $headers, TRUE ) )
        $headers = padCorsList ( preg_match ( '/^[A-Za-z0-9_, -]{0,1000}$/D', $asked ) ? $asked : '' );

      if ( $methods )
        header ( 'Access-Control-Allow-Methods: ' . implode ( ', ', $methods ) );

      if ( $headers )
        header ( 'Access-Control-Allow-Headers: ' . implode ( ', ', $headers ) );

      if ( $cors ['maxAge'] > 0 )
        header ( 'Access-Control-Max-Age: ' . $cors ['maxAge'] );

    }

    while ( ob_get_level () )
      ob_end_clean ();

    http_response_code ( 204 );
    header ( 'Cache-Control: no-cache, no-store' );

    $stop = 204;
    include PAD . 'exits/exit.php';

  }

  function padCorsConfig ( $cors ) {

    if ( ! $cors )
      return [];

    if ( ! is_array ( $cors ) ) {
      padError ( '$padCors must be an array - [ \'origins\' => [ \'https://app.example.com\' ] ], or [] for none' );
      return [];
    }

    foreach ( array_keys ( $cors ) as $key )
      if ( ! in_array ( $key, [ 'origins', 'methods', 'headers', 'credentials', 'maxAge', 'expose' ], TRUE ) ) {
        padError ( "\$padCors: there is no option named '" . padMakeSafe ( (string) $key, 40 )
                   . "' - origins, methods, headers, credentials, maxAge or expose" );
        return [];
      }

    $origins = [];

    foreach ( padCorsList ( $cors ['origins'] ?? [] ) as $one )
      $origins [] = ( $one === '*' ) ? '*' : strtolower ( rtrim ( $one, '/' ) );

    return [
      'origins'     => $origins,
      'methods'     => array_map ( 'strtoupper', padCorsList ( $cors ['methods'] ?? [] ) ),
      'headers'     => padCorsList ( $cors ['headers'] ?? [] ),
      'credentials' => (bool) ( $cors ['credentials'] ?? FALSE ),
      'maxAge'      => max ( 0, (int) ( $cors ['maxAge'] ?? 0 ) ),
      'expose'      => padCorsList ( $cors ['expose'] ?? [] )
    ];

  }

  // A list setting may be written as one text, comma separated. What could break a header
  // line - a line break, a colon - is no name of a method, a header or an origin's part
  // and is dropped with the entry that holds it.

  function padCorsList ( $value ) {

    $list = [];

    foreach ( is_array ( $value ) ? $value : explode ( ',', (string) $value ) as $one ) {

      $one = trim ( (string) $one );

      if ( $one !== '' and ! preg_match ( '/[\x00-\x1F\x7F,]/', $one ) )
        $list [] = $one;

    }

    return array_values ( array_unique ( $list ) );

  }

  // The Origin header is the browser's, but anything can send one: only a scheme, a host
  // and a port - what an origin is - is ever written back.

  function padCorsOrigin () {

    $origin = trim ( (string) ( $_SERVER ['HTTP_ORIGIN'] ?? '' ) );

    if ( preg_match ( '#^[a-z][a-z0-9+.-]*://([a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*|\[[0-9a-f:.]+\])(:[0-9]{1,5})?$#Di', $origin ) )
      return $origin;

    return '';

  }

?>
