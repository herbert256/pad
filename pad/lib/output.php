<?php
  

  function padWebSend ( $stop ) {  

    if ( ! $GLOBALS ['padOutput']       ) return;
    if ( isset ( $GLOBALS ['padSent'] ) ) return;

    if ( $GLOBALS ['padCacheStop'] == 200 ) {

      if ( $GLOBALS ['padCacheServerGzip'] and ! $GLOBALS ['padClientGzip'] )
        $output = padUnzip ( $GLOBALS ['padOutput'] );
      elseif ( ! $GLOBALS ['padCacheServerGzip'] and $GLOBALS ['padGzip'] and $GLOBALS ['padClientGzip'] )
        $output = padZip ( $GLOBALS ['padOutput'] );
      else
        $output = $GLOBALS ['padOutput'];
   
    } elseif ( $stop == 200 ) {

      if ( $GLOBALS ['padGzip'] and $GLOBALS ['padClientGzip'] )
        $output = padZip ( $GLOBALS ['padOutput'] );
      else
        $output = $GLOBALS ['padOutput'];
   
    } else

      $output = '';

    $GLOBALS ['padLen'] = strlen ( $output );

    padWebHeaders ( $stop );

    $GLOBALS ['padSent'] = TRUE;

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

    http_response_code ($stop);

  }


  function padWebPadHeaders ( $stop ) {

    padHeader       ('PAD: ' . $GLOBALS ['padSesID'] . '-' . $GLOBALS ['padReqID']);
    padWebStats     ();
    padWebNoHeaders ($stop);
    
    if ( $stop == 200 and $GLOBALS ['padGzip'] and $GLOBALS ['padClientGzip'] )
      padHeader ( 'Content-Encoding: gzip' );

    if ( $stop <> 302 and $stop <> 304 )
      padHeader ( 'Content-Type: ' . $GLOBALS ['padContentType'] );

    if ( $stop == 200 and $GLOBALS ['padLen'] )
      padHeader ( 'Content-Length: ' . $GLOBALS ['padLen'] );
    
    if ( ! isset ( $GLOBALS ['padCacheClientAge'] ) or ( $stop <> 200 and $stop <> 304 ) )
      padHeader ( 'Cache-Control: no-cache, no-store' );
    else
      padWebCacheHeaders ();

  }


  function padWebStats () {

    if ( ! $GLOBALS ['padInfo']      ) return;
    if ( ! $GLOBALS ['padInfoStats'] ) return;

    if ( ! isset ( $GLOBALS ['padInfoStatsJson'] ) )
      include PAD . 'info/types/stats/end.php';

    if ( isset ( $GLOBALS ['padInfoStatsJson'] ) )
      padHeader ( 'PAD-Stats: ' . $GLOBALS ['padInfoStatsJson'] );    

  }


  function padWebCacheHeaders () {

    if ( $GLOBALS ['padCacheClientAge'] )
      $age = $GLOBALS ['padCacheClientAge'] - ($_SERVER['REQUEST_TIME'] - $GLOBALS ['padTime']);
    else
      $age = 0;

    if ( $GLOBALS ['padCacheProxyAge'] ) {
      $type = 'public';
      $sage = $GLOBALS ['padCacheProxyAge'] - ($_SERVER['REQUEST_TIME'] - $GLOBALS ['padTime']);
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
    padHeader ('Etag: '          . '"' . $GLOBALS ['padEtag'] . '"');

  }
  

?>