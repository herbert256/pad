<?php

  // Fetching remote (and local) content for the {curl} tag, padPageGet and the curl data
  // type. Everything goes through padCurl, which always returns the same array shape -
  // url, input, options, result, type, info, headers, cookies, data (plus ERROR on
  // failure) - and leaves a copy in $padCurlLast.
  //
  // padCurl takes either a plain URL or an array with url, get, post, user/password,
  // cookies, headers and raw options. Its notable behaviour:
  //   - a name with no scheme is resolved first as a _data/ file, and only then treated
  //     as a page of this application ($padGoExt)
  //   - a URL on our own $padHost gets the session and request cookies, so a PAD page
  //     fetching another PAD page stays in the same request chain
  //   - the response is split on the reported header size; headers, Set-Cookie values and
  //     a content type ('html', 'xml', 'json', 'csv', 'yaml') are picked out, falling back
  //     to the filename in Content-Disposition and then to padContentType on the body
  //   - with $padCurlStats set the callee's PAD-Stats header is decoded into ['stats']
  //
  // padCurlMulti takes a whole array of such inputs and fetches them concurrently through
  // curl_multi, a rolling window at a time, returning an output per input under the same
  // key. The building and the parsing are padCurl's own - padCurlBuild readies the url and
  // options, padCurlParse turns a response into the output shape - so one fetch answers
  // the same whichever road it took. The regression crawl is the caller this exists for.
  //
  // padNoCurl is the fallback when ext-curl is missing: it just reads the URL as a file.
  // padCurlOpt sets a default that the caller's own options can override, and padCurlError
  // records the failure in the same array with result 999 instead of throwing.

  function padNoCurl ( $output ) {

    set_error_handler ( 'padErrorThrow' );
    $errorReporting = error_reporting (0);

    try {

      $result = padFileGet ( $output ['url'] );

      if ( $result === FALSE )
        return padCurlError ( $output, 'padFileGet = FALSE' );

    } catch (Throwable $e) {

      return padCurlError ( $output,  $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage() );

    }

    restore_error_handler ();
    error_reporting ( $errorReporting );

    $output ['data']   = $result;
    $output ['result'] = '200';
    $output ['type']   = padContentType ( $output ['data'] );

    return $output;

  }

  // Everything before the wire: the url resolved, the cookies added, the options filled in
  // with their defaults. A name that turns out to be a _data/ file is answered on the spot -
  // the output comes back with 'done' set and nothing left to fetch.

  function padCurlBuild ( $input ) {

    global $padGoExt, $padHost, $padPage, $padReqID, $padSesID;

    $output             = [];
    $output ['url']     = '';
    $output ['input']   = $input;
    $output ['options'] = [];
    $output ['result']  = '999';
    $output ['type']    = '';
    $output ['info']    = [];
    $output ['headers'] = [];
    $output ['cookies'] = [];
    $output ['data']    = '';

    $url = ( is_array($input) ) ? $input ['url'] : $input;

    if ( ! is_array($input) )
      $input = [];

    if ( isset($input['get']) )
      foreach ( $input['get'] as $key => $val )
        $url = padAddGet ( $url, $key, $val );

    $output ['url'] = $url;

    if ( ! strpos( $url, '://') ) {

      $check = padDataFileName ( $url );

      if ( $check ) {

        $output ['url']    = "file://$check";
        $output ['data']   = padDataFileData ( $check );
        $output ['type']   = padContentType  ( $output ['data'] );
        $output ['result'] = '200';
        $output ['done']   = TRUE;

        return $output;

      }

    }

    if ( ! strpos( $url, '://') ) {
      $url = $padGoExt . $url;
      $output ['url'] = $url;
    }

    if ( str_starts_with ( strtolower ( $url ), strtolower ( $padHost ) ) ) {
      $input ['cookies'] ['padSesID'] = $padSesID;
      $input ['cookies'] ['padReqID'] = $padReqID;
    }

    $options = $input ['options'] ?? [];

    padCurlOpt ($options, 'RETURNTRANSFER', true);
    padCurlOpt ($options, 'ENCODING',       'gzip' );
    padCurlOpt ($options, 'FOLLOWLOCATION', true);
    padCurlOpt ($options, 'HEADER',         true);

    // A fetch that can hang forever hangs whatever asked for it - the regression crawl most
    // of all. Callers can widen these through the options; without a bound there was none
    // at all (the audit's F-13).

    padCurlOpt ($options, 'CONNECTTIMEOUT', 10);
    padCurlOpt ($options, 'TIMEOUT',        120);
    padCurlOpt ($options, 'USERAGENT',      $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (X11; CrOS x86_64 13904.77.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.147 Safari/537.36 pad/10.0');
    padCurlOpt ($options, 'REFERER',        $padGoExt . $padPage);

    if ( isset($input['user']) )
      padCurlOpt ($options, 'USERPWD', $input['user'] . ":" . $input['password']);

    if ( isset($input['post']) ) {
      padCurlOpt ($options, 'POST', true);
      padCurlOpt ($options, 'POSTFIELDS', $input ['post']);
    }

    if ( isset($input['cookies']) ) {
      $cookies = '';
      foreach ( $input['cookies'] as $key => $val ) {
        if ($cookies)
          $cookies .= '; ';
        $cookies .= $key . '=' . $val;
      }
      padCurlOpt ($options, 'COOKIE', $cookies);
    }

    if ( isset($input['headers']) ) {
      $headers_in = [];
      foreach ( $input['headers'] as $key => $val )
        $headers_in [] = $key . ': ' . $val;
      padCurlOpt ($options, 'HTTPHEADER', $headers_in);
    }

    $output ['options'] = $options;

    return $output;

  }

  // Everything after the wire: the raw response split on the reported header size, the
  // headers, cookies and content type picked out, the body cleaned. The caller has already
  // put curl_getinfo's answer in ['info'].

  function padCurlParse ( $output, $result ) {

    global $padCurlStats, $padInfo;

    if ( isset ( $output ['info'] ['http_code'] ) )
      $output ['result'] = $output ['info'] ['http_code'];
    else
      $output ['result'] = 'xxx';

    $file = '';

    if ( isset($output ['info']['header_size']) and $output ['info']['header_size'] > 0 ) {

      $headers = explode( "\r\n", substr ( $result, 0, $output ['info'] ['header_size'] ) );

      foreach ($headers as $key => $val) {

        $work = explode ( ':', $val, 2 );

        $header = trim ( $work [0] ?? '' );
        $value  = trim ( $work [1] ?? '' );

        if ( $header and ! $value )

          $output ['headers'] ['http'] = $header;

        elseif ( $header and $value ) {

          if ( $header == 'Content-Disposition' and !$file)
            padBetween ($value, '"', '"', $before, $file, $after);

          if ( $header == 'Content-Type' )
            if     (strpos ($value, 'html')       !== FALSE) $output ['type'] = 'html';
            elseif (strpos ($value, 'xml')        !== FALSE) $output ['type'] = 'xml';
            elseif (strpos ($value, 'json')       !== FALSE) $output ['type'] = 'json';
            elseif (strpos ($value, 'javascript') !== FALSE) $output ['type'] = 'json';
            elseif (strpos ($value, 'csv')        !== FALSE) $output ['type'] = 'csv';
            elseif (strpos ($value, 'yaml')       !== FALSE) $output ['type'] = 'yaml';
            elseif (strpos ($value, 'yml')        !== FALSE) $output ['type'] = 'yaml';

          if ( $header == 'Set-Cookie') {
            $first = strpos ($value, '=');
            $last  = strpos ($value, ';');
            if ( $first !== FALSE and $last !== FALSE and $first > 0 and $last > $first )
             $output ['cookies'] [substr($value, 0, $first)] = substr($value, $first+1, $last-$first-1);
          }
          else
            $output ['headers'] [$header] = $value;

        }

      }

    }

    if ( isset ( $padCurlStats ) and isset ( $output ['headers'] ['PAD-Stats'] ) ) {
      $output ['stats'] = json_decode ( $output ['headers'] ['PAD-Stats'], TRUE ) ;
      $output ['stats'] ['curl'] = $output ['curlTime']
                                ?? (int) ( ( $output ['info'] ['total_time'] ?? 0 ) * 1e9 );
    }

    unset ( $output ['curlTime'] );

    if ( isset($output ['info']['header_size']) )
      $output ['data'] = trim(substr($result, $output ['info']['header_size']));
    else
      $output ['data'] = trim($result);

    $output ['data'] = str_replace ( "\r\n", "\n", $output ['data'] );

    if ( ! $output ['type'] and $file) {
      $pos = strrpos($file, '.');
      if ( $pos !== FALSE )
        $output ['type'] = substr($file, $pos+1);
    }

    if ( ! $output ['type'] )
      $output ['type'] = padContentType ( $output ['data'] );

    // The event traces $url, which was a local of the old one-piece padCurl - the split
    // keeps the name alive for it.

    $url = $output ['url'];

    if ( $padInfo )
      include PAD . 'events/curl.php';

    return $output;

  }

  function padCurl ($input) {

    global $padCurlLast, $padCurlStats;

    $output = padCurlBuild ( $input );

    if ( isset ( $output ['done'] ) ) {
      unset ( $output ['done'] );
      $padCurlLast = $output;
      return $output;
    }

    if ( ! function_exists ( 'curl_init') )
      return padNoCurl ( $output );

    $stats = isset ( $padCurlStats );

    set_error_handler ( 'padErrorThrow' );
    $errorReporting = error_reporting (0);

    try {

      $curl = curl_init ( $output ['url'] );

      if ($curl === FALSE)
        return padCurlError ($output, 'curl_init = FALSE');

      foreach ( $output ['options'] as $key => $val )
        curl_setopt ( $curl, constant('CURLOPT_'.$key), $val );

      if ( $stats ) $start = hrtime ( TRUE );
      $result = curl_exec ($curl);
      if ( $stats ) $end = hrtime ( TRUE );

      if ($result === FALSE)
        return padCurlError ($output, 'curl_exec = FALSE');

      $output ['info'] = curl_getinfo ($curl);

    } catch (Throwable $e) {

      return padCurlError ( $output,  $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage() );

    }

    restore_error_handler ();
    error_reporting ( $errorReporting );

    if ( $stats )
      $output ['curlTime'] = $end - $start;

    $output = padCurlParse ( $output, $result );

    $padCurlLast = $output;

    return $output;

  }

  // The concurrent form: the inputs are fetched through one curl_multi handle, at most
  // $window on the wire at once, and the outputs come back under the inputs' own keys.
  //
  // The window is the point: the local crawl talks to a server with a dozen workers, and
  // one fetch at a time leaves eleven of them idle. Everything the caller of padCurl may
  // rely on holds here too - same option defaults, same output shape, same 999-with-ERROR
  // on a failed transfer - except $padCurlLast, which is only meaningful for one fetch and
  // is left alone. Without ext-curl the inputs are simply fetched one by one.

  function padCurlMulti ( $inputs, $window = 12 ) {

    $results = [];

    if ( ! function_exists ( 'curl_multi_init' ) ) {

      foreach ( $inputs as $key => $input )
        $results [$key] = padCurl ( $input );

      return $results;

    }

    $outputs = [];
    $queue   = [];

    foreach ( $inputs as $key => $input ) {

      $output = padCurlBuild ( $input );

      if ( isset ( $output ['done'] ) ) {
        unset ( $output ['done'] );
        $results [$key] = $output;
      }

      else {
        $outputs [$key] = $output;
        $queue [] = $key;
      }

    }

    set_error_handler ( 'padErrorThrow' );
    $errorReporting = error_reporting (0);

    try {

      $multi   = curl_multi_init ();
      $flying  = [];

      while ( $queue or $flying ) {

        while ( $queue and count ( $flying ) < $window ) {

          $key  = array_shift ( $queue );
          $curl = curl_init ( $outputs [$key] ['url'] );

          foreach ( $outputs [$key] ['options'] as $name => $val )
            curl_setopt ( $curl, constant ( 'CURLOPT_' . $name ), $val );

          curl_multi_add_handle ( $multi, $curl );
          $flying [ spl_object_id ( $curl ) ] = [ $curl, $key ];

        }

        do {
          $state = curl_multi_exec ( $multi, $running );
        } while ( $state == CURLM_CALL_MULTI_PERFORM );

        if ( $running )
          curl_multi_select ( $multi, 0.1 );

        while ( $done = curl_multi_info_read ( $multi ) ) {

          $curl = $done ['handle'];
          list ( , $key ) = $flying [ spl_object_id ( $curl ) ];

          $result = curl_multi_getcontent ( $curl );
          $outputs [$key] ['info'] = curl_getinfo ( $curl );

          if ( $done ['result'] !== CURLE_OK or $result === FALSE or $result === NULL )
            $results [$key] = padCurlError ( $outputs [$key], 'curl_multi: ' . curl_strerror ( $done ['result'] ) );
          else
            $results [$key] = padCurlParse ( $outputs [$key], $result );

          unset ( $flying [ spl_object_id ( $curl ) ] );
          curl_multi_remove_handle ( $multi, $curl );

        }

      }

      curl_multi_close ( $multi );

    } catch ( Throwable $e ) {

      // Whatever was in the air when something threw comes back as the failure it is, so
      // the caller still gets one output per input.

      foreach ( $inputs as $key => $input )
        if ( ! isset ( $results [$key] ) )
          $results [$key] = padCurlError ( $outputs [$key] ?? padCurlBuild ( $input ),
                                           $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage() );

    }

    restore_error_handler ();
    error_reporting ( $errorReporting );

    return $results;

  }

  function padCurlOpt (&$options, $name, $value) {

    if ( ! isset ( $options [$name] ) )
      $options [$name] = $value;

  }

  function padCurlError ( $output, $error ) {

    global $padCurlLast;

    $output ['ERROR']  = $error;
    $output ['result'] = '999';

    $padCurlLast = $output;

    return $output;

  }

?>