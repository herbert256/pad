<?php

 
  function pStop ($stop) {

    $GLOBALS['pStop'] = $stop;
    $GLOBALS['pLen']  = ( $stop == 200 ) ? strlen($GLOBALS['pOutput']) : 0;

    if ( $GLOBALS['pTrack_db_session'] or $GLOBALS['pTrack_db_request'] )
      pTrack_db_session ();

    if ( $GLOBALS['pTrack_file_request'] )
      pTrack_file_request ();

    pClose_session ();
    pEmpty_buffers ();

    if ( ! isset($GLOBALS['pSent']) ) {

      $GLOBALS['pSent'] = TRUE;

      pHeaders ($stop);
    
      if ( $stop == 200 ) {

        if ( $GLOBALS['pCache_stop'] and $GLOBALS['pCache_server_gzip'] and $GLOBALS['pClient_gzip']  )
          echo $GLOBALS['pOutput'];
        elseif ( $GLOBALS['pCache_stop'] and $GLOBALS['pCache_server_gzip'] and ! $GLOBALS['pClient_gzip'] )
          echo pUnzip ( $GLOBALS['pOutput'] );
        elseif ( $GLOBALS['pClient_gzip'] )
          echo pZip ( $GLOBALS['pOutput'] );
        else 
          echo $GLOBALS['pOutput'];

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
    elseif ( $stop == 200 and $GLOBALS ['pClient_gzip'] )
      pHeader ('Content-Encoding: gzip');

    if ( $stop <> 302 and $stop <> 304)
      pHeader ('Content-Type: text/html; charset=UTF-8');

    if ( $stop == 200 and $GLOBALS['pLen'] )
      pHeader ('Content-Length: ' . $GLOBALS['pLen']);
    
    if ( ($stop <> 200 and $stop <> 304) or ! $GLOBALS['pCache'] )
      pHeader ('Cache-Control: no-cache, no-store');
    else
      pCache_headers ();

    pTiming_close ();

  }


  function pCache_headers () {

    if ( $GLOBALS ['pCache_client_age'] )
      $age = $GLOBALS ['pCache_client_age'] - ($_SERVER['REQUEST_TIME'] - $GLOBALS['pTime']);
    else
      $age = 0;

    if ( $GLOBALS ['pCache_proxy_age'] ) {
      $type = 'public';
      $sage = $GLOBALS ['pCache_proxy_age'] - ($_SERVER['REQUEST_TIME'] - $GLOBALS['pTime']);
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
    pHeader ('Etag: '          . '"' . $GLOBALS['pEtag'] . '"');

  }


  function pExit () {

    $GLOBALS['pSkip_shutdown']      = TRUE;
    $GLOBALS['pSkip_boot_shutdown'] = TRUE;

    include PAD . 'exits/trace.php';
    
    exit;

  }


?>