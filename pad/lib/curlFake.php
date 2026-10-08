<?php

  // HTTP fakes for tests: the remote services a page talks to answer from a list the test
  // gives, so a test runs without the network, the same on every run, and can see what
  // the page asked - what Laravel's Http::fake and Rails' WebMock give.
  //
  //   padCurlFake ( [
  //     'https://api.example.com/rates*' => [ 'status' => 200, 'data' => '{"eur":1.08}',
  //                                           'headers' => [ 'Content-Type' => 'application/json' ] ],
  //     'https://api.example.com/down'   => 503,
  //     '*'                              => 'ok'
  //   ] );
  //
  //   {curl 'https://api.example.com/rates'}         answers {"eur":1.08}
  //   padCurlRecorded ()                              [ [ url, method, headers, post ], ... ]
  //
  // padCurlFake      fakes the fetches of the rest of the request; called again, its fakes
  //                  are added to the ones there are, and the record starts afresh
  // padCurlFakeStop  ends the faking - the record stays readable
  // padCurlRecorded  every fetch made while faking, in order
  // padCurlFaking    whether fetches are faked - padCurl and padCurlMulti ask
  // padCurlFakeAnswer the answer of a fetch while faking, in padCurl's own shape
  //
  // A fake is a URL pattern - * stands for anything, the rest is the URL as it is fetched,
  // its query string included, with or without its scheme - and an answer: an array with
  // status (200), data (the body: text, or an array sent as JSON) and headers; a text
  // alone, the body of a 200; a number alone, a status with no body; or a closure that gets
  // the request - url, method, headers, post - and answers one of these. The first pattern
  // that matches answers, in the order given; '*' is the answer for whatever else is
  // fetched. Without one, a fetch that no fake matches is an error: a stray request, which
  // would go out to the real service.
  //
  // The answer goes through padCurlParse as a real response does, so its content type,
  // cookies and headers are read alike, and every road a fetch takes is faked: {curl},
  // padCurl, data='https://...', a _data/*.curl file, padCurlCached and padPrefetch. A
  // fetch with a ttl does not read or keep the cache while faking - a fake answer kept
  // would be served to the real requests after it. A _data/ file read by name is no fetch
  // and is read as always.
  //
  // The fakes live in a static of this file, for the request that set them: only PHP that
  // runs - a test page, a _tests/ case - can switch them on. No request value reaches them.

  function padCurlFake ( $fakes ) {

    if ( ! is_array ( $fakes ) ) {
      padError ( 'padCurlFake: the fakes are an array - URL pattern => answer - not ' . get_debug_type ( $fakes ) );
      return FALSE;
    }

    foreach ( $fakes as $pattern => $answer ) {

      if ( ! is_string ( $pattern ) or $pattern === '' ) {
        padError ( "padCurlFake: a fake is named by a URL pattern like 'https://api.example.com/*' - not " . padCacheAppShow ( $pattern ) );
        return FALSE;
      }

      if ( ! ( is_array ( $answer ) or is_string ( $answer ) or is_int ( $answer ) or $answer instanceof Closure ) ) {
        padError ( "padCurlFake: the answer for '" . padMakeSafe ( $pattern, 60 ) . "' is an array, a body, a status or a closure - not "
                 . get_debug_type ( $answer ) );
        return FALSE;
      }

    }

    $store = & padCurlFakeStore ();

    $store ['on']       = TRUE;
    $store ['fakes']    = array_merge ( $store ['fakes'], $fakes );
    $store ['recorded'] = [];

    return TRUE;

  }

  function padCurlFakeStop () {

    $store = & padCurlFakeStore ();

    $store ['on']    = FALSE;
    $store ['fakes'] = [];

    return TRUE;

  }

  function padCurlRecorded () {

    return padCurlFakeStore () ['recorded'];

  }

  function padCurlFaking () {

    return padCurlFakeStore () ['on'];

  }

  function & padCurlFakeStore () {

    static $store = [ 'on' => FALSE, 'fakes' => [], 'recorded' => [] ];

    return $store;

  }

  // $output is what padCurlBuild readied: the URL as it would go out and the options it
  // would go out with.

  function padCurlFakeAnswer ( $output ) {

    $store   = & padCurlFakeStore ();
    $options = $output ['options'];
    $request = [ 'url'     => $output ['url'],
                 'method'  => ( $options ['POST'] ?? FALSE ) ? 'POST' : ( $options ['CUSTOMREQUEST'] ?? 'GET' ),
                 'headers' => padCurlFakeHeaders ( $options ['HTTPHEADER'] ?? [] ),
                 'post'    => $options ['POSTFIELDS'] ?? NULL ];

    $store ['recorded'] [] = $request;

    $answer = NULL;

    foreach ( $store ['fakes'] as $pattern => $one )
      if ( $pattern !== '*' and padCurlFakeMatch ( $pattern, $output ['url'] ) ) {
        $answer = $one;
        break;
      }

    $answer ??= $store ['fakes'] ['*'] ?? NULL;

    if ( $answer === NULL ) {
      padError ( "padCurlFake: a stray request - no fake answers " . padMakeSafe ( $output ['url'], 120 )
               . " - add its pattern, or '*' for every other URL" );
      return padCurlError ( $output, 'padCurlFake: no fake answers this URL' );
    }

    if ( $answer instanceof Closure )
      $answer = $answer ( $request );

    if ( is_string ( $answer ) )
      $answer = [ 'data' => $answer ];
    elseif ( is_int ( $answer ) )
      $answer = [ 'status' => $answer ];
    elseif ( ! is_array ( $answer ) )
      $answer = [ 'status' => 500, 'data' => 'padCurlFake: the closure answered ' . get_debug_type ( $answer ) ];

    $status  = (int) ( $answer ['status'] ?? 200 );
    $body    = $answer ['data'] ?? $answer ['body'] ?? '';
    $headers = (array) ( $answer ['headers'] ?? [] );

    if ( is_array ( $body ) ) {
      $body = json_encode ( $body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
      $headers += [ 'Content-Type' => 'application/json' ];
    }

    $head = "HTTP/1.1 $status\r\n";

    foreach ( $headers as $name => $value )
      foreach ( (array) $value as $line )
        $head .= str_replace ( [ "\r", "\n" ], '', "$name: $line" ) . "\r\n";

    $head .= "\r\n";

    $output ['info'] = [ 'url' => $output ['url'], 'http_code' => $status, 'header_size' => strlen ( $head ), 'total_time' => 0 ];

    $output = padCurlParse ( $output, $head . (string) $body );

    $GLOBALS ['padCurlLast'] = $output;

    return $output;

  }

  // * is anything; the rest stands for itself. A pattern without a scheme matches the URL
  // without its own.

  function padCurlFakeMatch ( $pattern, $url ) {

    $regex = '#^' . str_replace ( '\*', '.*', preg_quote ( $pattern, '#' ) ) . '$#Ds';

    if ( preg_match ( $regex, $url ) )
      return TRUE;

    return ! str_contains ( $pattern, '://' ) and preg_match ( $regex, preg_replace ( '#^[a-z][a-z0-9+.-]*://#i', '', $url ) );

  }

  function padCurlFakeHeaders ( $lines ) {

    $headers = [];

    foreach ( (array) $lines as $line ) {
      $pair = explode ( ':', (string) $line, 2 );
      $headers [ trim ( $pair [0] ) ] = trim ( $pair [1] ?? '' );
    }

    return $headers;

  }

?>
