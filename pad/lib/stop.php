<?php

 
  function padStop ($stop) {

    $GLOBALS ['padStop'] = $stop;
    $GLOBALS ['padLen']  = ( $stop == 200 ) ? strlen($GLOBALS ['padOutput']) : 0;

    if ( $GLOBALS ['padTrack_db_session'] or $GLOBALS ['padTrack_db_request'] )
      padTrackDbSession ();

    if ( $GLOBALS ['padTrack_file_request'] )
      padTrackFileRequest ();

    padCloseSession ();
    padEmptyBuffers ();

    if ( ! isset($GLOBALS ['padSent']) ) {

      $GLOBALS ['padSent'] = TRUE;

      padHeaders ($stop);
    
      if ( $stop == 200 ) {

        if ( $GLOBALS ['padCache_stop'] and $GLOBALS ['padCache_server_gzip'] and $GLOBALS ['padClient_gzip']  )
          echo $GLOBALS ['padOutput'];
        elseif ( $GLOBALS ['padCache_stop'] and $GLOBALS ['padCache_server_gzip'] and ! $GLOBALS ['padClient_gzip'] )
          echo padUnzip ( $GLOBALS ['padOutput'] );
        elseif ( $GLOBALS ['padClient_gzip'] )
          echo padZip ( $GLOBALS ['padOutput'] );
        else 
          echo $GLOBALS ['padOutput'];

      }

    }  

    padExit ();

  }


  function padHeaders ($stop) {

    if ( headers_sent () )
      return;

    if ( $stop == 500 )
      padHeader ('HTTP/1.0 500 Internal Server Error' );
    elseif ( $stop == 304 )
      padHeader ('HTTP/1.1 304 Not Modified');
    elseif ( $stop == 200 and $GLOBALS ['padClient_gzip'] )
      padHeader ('Content-Encoding: gzip');

    if ( $stop <> 302 and $stop <> 304)
      padHeader ('Content-Type: text/html; charset=UTF-8');

    if ( $stop == 200 and $GLOBALS ['padLen'] )
      padHeader ('Content-Length: ' . $GLOBALS ['padLen']);
    
    if ( ($stop <> 200 and $stop <> 304) or ! $GLOBALS ['padCache'] )
      padHeader ('Cache-Control: no-cache, no-store');
    else
      padCacheHeaders ();

    padTimingClose ();

  }


  function padCacheHeaders () {

    if ( $GLOBALS ['padCache_client_age'] )
      $age = $GLOBALS ['padCache_client_age'] - ($_SERVER['REQUEST_TIME'] - $GLOBALS ['padTime']);
    else
      $age = 0;

    if ( $GLOBALS ['padCache_proxy_age'] ) {
      $type = 'public';
      $sage = $GLOBALS ['padCache_proxy_age'] - ($_SERVER['REQUEST_TIME'] - $GLOBALS ['padTime']);
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


  function padExit () {

    $GLOBALS ['padSkip_shutdown']      = TRUE;
    $GLOBALS ['padSkip_boot_shutdown'] = TRUE;

    include PAD . 'exits/trace.php';
    
    exit;

  }


?>