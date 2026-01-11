<?php

  function padWebSend ( $stop ) {

    global $padCacheServerGzip, $padCacheStop, $padClientGzip, $padGzip, $padLen, $padOutput, $padSent;

    if ( ! $padOutput       ) return;
    if ( isset ( $padSent ) ) return;

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

    ignore_user_abort (true);

    if ( function_exists ( 'fastcgi_finish_request') )
      fastcgi_finish_request();

  }

  function padDownLoadHeaders ( $contentType, $fileName, $length ) {

    global $padSent, $padStop;

    if ( isset ( $padSent ) )
      padError ( "Content already sent with download" );

    if ( $padStop <> 200 )
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

  }

  function padWebNoHeaders ( $stop ) {

    if ( ! headers_sent () )
      http_response_code ($stop);

  }

  function padWebPadHeaders ( $stop ) {

    global $padCacheClientAge, $padClientGzip, $padContentType, $padGzip, $padLen, $padReqID, $padSesID;

    padHeader       ('PAD: ' . $padSesID . '-' . $padReqID);
    padWebStats     ();
    padWebNoHeaders ($stop);

    if ( $stop == 200 and $padGzip and $padClientGzip )
      padHeader ( 'Content-Encoding: gzip' );

    if ( $stop <> 302 and $stop <> 304 )
      padHeader ( 'Content-Type: ' . $padContentType );

    if ( $stop == 200 and $padLen )
      padHeader ( 'Content-Length: ' . $padLen );

    if ( ! isset ( $padCacheClientAge ) or ( $stop <> 200 and $stop <> 304 ) )
      padHeader ( 'Cache-Control: no-cache, no-store' );
    else
      padWebCacheHeaders ();

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

  function padWebCacheHeaders () {

    global $padCacheClientAge, $padCacheProxyAge, $padEtag, $padTime;

    if ( $padCacheClientAge )
      $age = $padCacheClientAge - ($_SERVER['REQUEST_TIME'] - $padTime);
    else
      $age = 0;

    if ( $padCacheProxyAge ) {
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
    padHeader ('Vary: '          . 'Content-Encoding');
    padHeader ('Date: '          . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME']        ) . ' GMT');;
    padHeader ('Expires: '       . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME'] + $age ) . ' GMT');
    padHeader ('Etag: '          . '"' . $padEtag . '"');

  }

?>