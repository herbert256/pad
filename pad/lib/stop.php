<?php

 
  function pad_stop ($stop) {

    $GLOBALS['pad_stop'] = $stop;
    $GLOBALS['pad_len']  = ( $stop == 200 ) ? strlen($GLOBALS['pad_output']) : 0;

    if ( $GLOBALS['pad_track_db_session'] or $GLOBALS['pad_track_db_request'] )
      pad_track_db_session ();

    if ( $GLOBALS['pad_track_file_request'] )
      pad_track_file_request ();

    pad_close_session ();
    pad_empty_buffers ();

    if ( ! isset($GLOBALS['pad_sent']) ) {

      $GLOBALS['pad_sent'] = TRUE;

      pad_headers ();
    
      if ( $stop == 200 ) {

        if ( $GLOBALS['pad_cache_stop'] and $GLOBALS['pad_cache_server_gzip'] and $GLOBALS['pad_client_gzip']  )
          echo $GLOBALS['pad_output'];
        elseif ( $GLOBALS['pad_cache_stop'] and $GLOBALS['pad_cache_server_gzip'] and ! $GLOBALS['pad_client_gzip'] )
          echo pad_unzip ( $GLOBALS['pad_output'] );
        elseif ( $GLOBALS['pad_client_gzip'] )
          echo pad_zip ( $GLOBALS['pad_output'] );
        else 
          echo $GLOBALS['pad_output'];

      }

    }  

    pad_exit ();

  }


  function pad_headers () {

    if ( headers_sent () )
      return;

    $stop = $GLOBALS['pad_stop'];

    if ( $stop == 500 )
      pad_header ('HTTP/1.0 500 Internal Server Error' );
    elseif ( $stop == 304 )
      pad_header ('HTTP/1.1 304 Not Modified');
    elseif ( $stop == 200 and $GLOBALS ['pad_client_gzip'] )
      pad_header ('Content-Encoding: gzip');

    if ( $stop <> 302 and $stop <> 304)
      pad_header ('Content-Type: text/html; charset=UTF-8');

    if ( $stop == 200 )
      pad_header ('Content-Length: ' . $GLOBALS['pad_len']);
    
    if ( ($stop <> 200 and $stop <> 304) or ! $GLOBALS['pad_cache'] )
      pad_header ('Cache-Control: no-cache, no-store');
    else
      pad_cache_headers ();

    pad_timing_close ();

  }


  function pad_cache_headers () {

    if ( $GLOBALS ['pad_cache_client_age'] )
      $age = $GLOBALS ['pad_cache_client_age'] - ($_SERVER['REQUEST_TIME'] - $GLOBALS['pad_time']);
    else
      $age = 0;

    if ( $GLOBALS ['pad_cache_proxy_age'] ) {
      $type = 'public';
      $sage = $GLOBALS ['pad_cache_proxy_age'] - ($_SERVER['REQUEST_TIME'] - $GLOBALS['pad_time']);
    } else {
      $type = 'private';
      $sage = 0;
    }
     
    if ( $age  < 0 ) $age  = 0;
    if ( $sage < 0 ) $sage = 0;
    
    $extra = 'no-transform, must-revalidate, proxy-revalidate';

    pad_header ('Cache-Control: ' . "$type, max-age=$age, s-maxage=$sage, $extra");
    pad_header ('Vary: '          . 'Content-Encoding');
    pad_header ('Date: '          . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME']        ) . ' GMT');;
    pad_header ('Expires: '       . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME'] + $age ) . ' GMT');
    pad_header ('Etag: '          . '"' . $GLOBALS['pad_etag'] . '"');

  }


  function pad_exit () {

    $GLOBALS['pad_skip_shutdown']      = TRUE;
    $GLOBALS['pad_skip_boot_shutdown'] = TRUE;

    include PAD . 'exits/trace.php';
    
    exit;

  }


?>