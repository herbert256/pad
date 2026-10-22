<?php

  // Sends the finished page to the browser: gzip decision, HTTP headers, body, and the
  // early hand-back to the web server. Called from the exit path (lib/exit.php,
  // exits/) and by the download tags.
  //
  // padWebSend           the one place the body is echoed. Reconciles how $padOutput is
  //                      currently stored (cached gzipped or not) with what the client
  //                      accepts, records $padLen, sets $padSent, then flushes and calls
  //                      fastcgi_finish_request so shutdown work costs the user nothing
  // padDownLoadHeaders   attachment headers for the download output type
  // padWebHeaders        header entry point, guarded so it happens once, choosing between
  // padWebNoHeaders      just the status code ($padWebNoHeaders mode), and
  // padWebPadHeaders     the full set: PAD id, stats, encoding, content type and length,
  //                      plus caching - and either way adding the security headers
  //                      (padSecurityHeaders in lib/security.php)
  // padWebStats          adds the PAD-Stats header when info stats are on
  // padWebCacheHeaders   Cache-Control, Date, Expires and Etag, with the ages counted
  //                      down by how long this request has already taken
  // padAcceptsEncoding   whether an Accept-Encoding header allows a content coding, read
  //                      with its quality values
  // padOutputAdd         what the debug toolbar and the live reload script add to a page
  //                      on its way out

  function padWebSend ( $stop ) {

    global $padCacheServerGzip, $padCacheStop, $padClientGzip, $padGzip, $padLen, $padOutput, $padSent;

    // The empty test is strict: a page whose whole output is '0' is still a page.

    if ( ( $padOutput ?? '' ) === '' ) return;
    if ( isset ( $padSent )          ) return;

    if ( $padCacheStop == 200 ) {

      if ( $padCacheServerGzip and ! $padClientGzip )
        $output = padUnzip ( $padOutput );
      elseif ( ! $padCacheServerGzip and $padGzip and $padClientGzip )
        $output = padZip ( $padOutput );
      else
        $output = $padOutput;

    } elseif ( $stop == 200 ) {

      if ( $padGzip and $padClientGzip )
        $output = padZip ( $padOutput );
      else
        $output = $padOutput;

    } else

      $output = '';

    $padLen = strlen ( $output );

    padWebHeaders ( $stop );

    $padSent = TRUE;

    if ( $stop == 200 )
      echo $output;

    flush ();

    // Once the body is out nothing more may be written: PHP's own report of an error -
    // under the php error action an application's exception or warning in a shutdown
    // function - landed behind the body, past the Content-Length just sent (padAnswerSent).

    ini_set ( 'display_errors', '0' );

    ignore_user_abort (true);

    if ( function_exists ( 'fastcgi_finish_request') )
      fastcgi_finish_request();

  }

  function padDownLoadHeaders ( $contentType, $fileName, $length ) {

    global $padSent, $padStop;

    if ( isset ( $padSent ) )
      padError ( "Content already sent with download" );

    if ( $padStop != 200 )
      padError ( "HTTP status not 200 with download" );

    padHeader ( "Content-Type: $contentType");
    padHeader ( "Content-Transfer-Encoding: Binary");
    padHeader ( "Content-Disposition: attachment; filename=\"$fileName\"");
    padHeader ( "Content-Length: $length");

  }

  function padWebHeaders ( $stop ) {

    global $padWebNoHeaders, $padSent;

    if ( isset ( $padSent ) or padSecondTime ( 'webHeaders' ) )
      return;

    if ( $padWebNoHeaders )
      padWebNoHeaders  ( $stop );
    else
      padWebPadHeaders ( $stop );

    padSecurityHeaders ();

  }

  // A status the page's own PHP set with http_response_code() stands when the engine would
  // only have said 200: a page could not answer its own 404 or 410 before.

  function padWebNoHeaders ( $stop ) {

    if ( headers_sent () )
      return;

    $current = http_response_code ();

    if ( $stop == 200 and $current and $current != 200 )
      return;

    http_response_code ($stop);

  }

  function padWebPadHeaders ( $stop ) {

    global $padCacheClientAge, $padCacheServerGzip, $padCacheStop, $padClientGzip, $padContentType, $padGzip, $padLen, $padReqID, $padSesID;

    padHeader       ('PAD: ' . $padSesID . '-' . $padReqID);
    padWebStats     ();
    padWebNoHeaders ($stop);

    // The same decision padWebSend makes about the body: a cache hit keeps its stored gzip
    // for a client that accepts it, and saying so only when the on-the-fly $padGzip was
    // configured shipped compressed bytes with no Content-Encoding at all.

    // A 304 carries the ETag its 200 would - the gzip body's tag when that 200 came gzipped,
    // from the cache's store as from the on-the-fly $padGzip (RFC 9110): it said the identity
    // tag, which a cache holding the gzip copy may not take for the one it holds.

    $padWebZip  = ( $padClientGzip and ( $padGzip or ( ( $padCacheStop == 200 or $padCacheStop == 304 ) and $padCacheServerGzip ) ) );
    $padWebGzip = ( $stop == 200 and $padWebZip );

    if ( $padWebGzip )
      padHeader ( 'Content-Encoding: gzip' );

    // Whenever the body can come compressed, the response depends on Accept-Encoding and
    // says so - for a cache along the way. It used to say Vary: Content-Encoding, the name
    // of a response header, and only on the cached path; the on-the-fly gzip sent nothing.

    // A locale picked from Accept-Language makes the response depend on that header too -
    // and on Cookie, where a padLang cookie can choose it (lib/locale.php). The answer to
    // that visitor is private (padWebCacheHeaders), but a shared cache that kept the public
    // one a guest got, under the address and Accept-Language alone, handed it to the next
    // visitor whose cookie chose another locale, the server never asked. Vary: Cookie keeps
    // the public answer for the visitors who send the same cookies - the guests - where
    // making every answer private would keep it for none.

    $padWebVary = [];

    if ( $padGzip or $padCacheServerGzip )
      $padWebVary [] = 'Accept-Encoding';

    if ( count ( $GLOBALS ['padLocales'] ?? [] ) )
      array_push ( $padWebVary, 'Accept-Language', 'Cookie' );

    // A page that exposes its data answers JSON or CSV to an Accept header that asks for
    // them (lib/expose.php), so its response depends on that header as well.

    if ( count ( (array) ( $GLOBALS ['padExpose'] ?? [] ) ) )
      $padWebVary [] = 'Accept';

    // An answer that names the visitor's origin (inits/_lib/cors.php) depends on Origin; the CORS
    // step said so early, and this header replaces that one.

    if ( $GLOBALS ['padCorsVary'] ?? FALSE )
      $padWebVary [] = 'Origin';

    if ( $padWebVary )
      padHeader ( 'Vary: ' . implode ( ', ', $padWebVary ) );

    if ( $stop != 302 and $stop != 304 )
      padHeader ( 'Content-Type: ' . $padContentType );

    if ( $stop == 200 and $padLen )
      padHeader ( 'Content-Length: ' . $padLen );

    if ( ! isset ( $padCacheClientAge ) or ( $stop != 200 and $stop != 304 ) )
      padHeader ( 'Cache-Control: no-cache, no-store' );
    else
      padWebCacheHeaders ( $padWebZip );

  }

  function padWebStats () {

    global $padInfo, $padInfoStats, $padInfoStatsJson;

    if ( ! $padInfo      ) return;
    if ( ! $padInfoStats ) return;

    if ( ! isset ( $padInfoStatsJson ) )
      include PAD . 'info/types/stats/end.php';

    if ( isset ( $padInfoStatsJson ) )
      padHeader ( 'PAD-Stats: ' . $padInfoStatsJson );

  }

  // gzip;q=0 refuses gzip, and * stands for every coding the header does not name. The
  // test used to be a substring search, which sent gzip to the client that refused it.

  function padAcceptsEncoding ( $header, $coding ) {

    $any = FALSE;

    foreach ( explode ( ',', $header ) as $one ) {

      $parts = explode ( ';', $one );
      $name  = strtolower ( trim ( $parts [0] ) );
      $q     = 1.0;

      foreach ( array_slice ( $parts, 1 ) as $parm )
        if ( preg_match ( '/^\s*q\s*=\s*([0-9.]+)\s*$/i', $parm, $match ) )
          $q = (float) $match [1];

      if ( $name == $coding )
        return $q > 0;

      if ( $name == '*' )
        $any = $q > 0;

    }

    return $any;

  }

  // A response that sets a cookie is private whatever the proxy age: a shared cache along
  // the way would hand that visitor's Set-Cookie to the next one. The gzip body has an ETag
  // of its own, the identity one with -gzip behind it - the two bodies shared one, which a
  // cache may not mix - and Last-Modified says when the page was made, where nothing did.
  //
  // So is the answer to a visitor with an identity - a cookie of their own, a session, an
  // Authorization header (padCacheIdentity, lib/cache.php): the page cache builds it fresh
  // because it may be theirs alone, and it went out public with an s-maxage, so a shared
  // cache kept "Hello Ann" and handed it to every visitor after her. A padLang cookie that
  // chose the page's locale among $padLocales (lib/locale.php) counts here too, where the
  // page cache, whose key holds the locale, lets it pass: a shared cache keys on the address
  // and Vary: Accept-Language only, and handed the Dutch page to the next English visitor.

  function padWebCacheHeaders ( $gzip = FALSE ) {

    global $padCacheClientAge, $padCacheProxyAge, $padEtag, $padTime;

    $cookie = FALSE;

    foreach ( headers_list () as $header )
      if ( stripos ( $header, 'Set-Cookie:' ) === 0 )
        $cookie = TRUE;

    if ( $padCacheClientAge )
      $age = $padCacheClientAge - ($_SERVER['REQUEST_TIME'] - $padTime);
    else
      $age = 0;

    $chosen = ( count ( $GLOBALS ['padLocales'] ?? [] ) and isset ( $_COOKIE ['padLang'] ) );

    if ( $padCacheProxyAge and ! $cookie and ! $chosen and ! padCacheIdentity () ) {
      $type = 'public';
      $sage = $padCacheProxyAge - ($_SERVER['REQUEST_TIME'] - $padTime);
    } else {
      $type = 'private';
      $sage = 0;
    }

    if ( $age  < 0 ) $age  = 0;
    if ( $sage < 0 ) $sage = 0;

    $extra = 'no-transform, must-revalidate, proxy-revalidate';

    padHeader ('Cache-Control: ' . "$type, max-age=$age, s-maxage=$sage, $extra");
    padHeader ('Date: '          . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME']        ) . ' GMT');;
    padHeader ('Expires: '       . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME'] + $age ) . ' GMT');
    padHeader ('Last-Modified: ' . gmdate('D, d M Y H:i:s', $padTime                       ) . ' GMT');

    // No body to describe, no tag: the headers of a {flush} go out before the page is whole
    // (lib/flush.php), and said Etag: "", one tag for every flushed page.

    if ( (string) $padEtag !== '' )
      padHeader ('Etag: '        . '"' . $padEtag . ( $gzip ? '-gzip' : '' ) . '"');

  }

  // What a development helper adds to a page on its way out - the toolbar, the live reload
  // script: only to a web page answering 200 as text/html, before its last </body> or at
  // the end. A page from the page cache may still be gzipped; the addition goes into the
  // page itself, and the writer then sees a plain body. The answer to a live event is no
  // page but the inside of one region (lib/live.php): the script swaps it in, and every
  // event put another toolbar into the region.

  function padOutputAdd ( $html ) {

    global $padOutput, $padStop, $padContentType, $padCacheStop, $padCacheServerGzip;

    if ( $padStop != 200 or ! str_starts_with ( (string) $padContentType, 'text/html' ) or padLive () !== '' )
      return;

    if ( $padCacheStop == 200 and $padCacheServerGzip ) {
      $padOutput          = padUnzip ( $padOutput );
      $padCacheServerGzip = FALSE;
    }

    $at = strripos ( $padOutput, '</body>' );

    $padOutput = ( $at === FALSE ) ? $padOutput . $html : substr_replace ( $padOutput, $html, $at, 0 );

  }

?>
