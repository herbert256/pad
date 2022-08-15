<?php

 
  function pStop ($stop) {

    $GLOBALS ['padStop'] = $stop;
    $GLOBALS ['padLen']  = ( $stop == 200 ) ? strlen($GLOBALS ['padOutput']) : 0;

    if ( $GLOBALS ['padTrack_db_session'] or $GLOBALS ['padTrack_db_request'] )
      pTrack_db_session ();

    if ( $GLOBALS ['padTrack_file_request'] )
      pTrack_file_request ();

    pClose_session ();
    pEmpty_buffers ();

    if ( ! isset($GLOBALS ['padSent']) ) {

      $GLOBALS ['padSent'] = TRUE;

      pHeaders ($stop);
    
      if ( $stop == 200 ) {

        if ( $GLOBALS ['padCache_stop'] and $GLOBALS ['padCache_server_gzip'] and $GLOBALS ['padClient_gzip']  )
          echo $GLOBALS ['padOutput'];
        elseif ( $GLOBALS ['padCache_stop'] and $GLOBALS ['padCache_server_gzip'] and ! $GLOBALS ['padClient_gzip'] )
          echo pUnzip ( $GLOBALS ['padOutput'] );
        elseif ( $GLOBALS ['padClient_gzip'] )
          echo pZip ( $GLOBALS ['padOutput'] );
        else 
          echo $GLOBALS ['padOutput'];

      }

    }  

    pExit ();

  }


  function pHeaders ($stop) {

    if ( headers_sent () )
      return;

    if ( $stop == 500 )
      pHeader ('HTTP/1.0 500 Internal Server Error' );
    elseif ( $stop == 304 )
      pHeader ('HTTP/1.1 304 Not Modified');
    elseif ( $stop == 200 and $GLOBALS ['padClient_gzip'] )
      pHeader ('Content-Encoding: gzip');

    if ( $stop <> 302 and $stop <> 304)
      pHeader ('Content-Type: text/html; charset=UTF-8');

    if ( $stop == 200 and $GLOBALS ['padLen'] )
      pHeader ('Content-Length: ' . $GLOBALS ['padLen']);
    
    if ( ($stop <> 200 and $stop <> 304) or ! $GLOBALS ['padCache'] )
      pHeader ('Cache-Control: no-cache, no-store');
    else
      pCache_headers ();

    pTiming_close ();

  }


  function pCache_headers () {

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

    pHeader ('Cache-Control: ' . "$type, max-age=$age, s-maxage=$sage, $extra");
    pHeader ('Vary: '          . 'Content-Encoding');
    pHeader ('Date: '          . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME']        ) . ' GMT');;
    pHeader ('Expires: '       . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME'] + $age ) . ' GMT');
    pHeader ('Etag: '          . '"' . $GLOBALS ['padEtag'] . '"');

  }


  function pExit () {

    $GLOBALS ['padSkip_shutdown']      = TRUE;
    $GLOBALS ['padSkip_boot_shutdown'] = TRUE;

    include PAD . 'exits/trace.php';
    
    exit;

  }


?>