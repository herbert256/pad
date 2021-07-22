<?php

 
  function pad_stop ($stop) {

    $GLOBALS['pad_stop'] = $stop;
    $GLOBALS['pad_len']  = ( $stop == 200 ) ? strlen($GLOBALS['pad_output']) : 0;

    if ( $GLOBALS['pad_track_db_session'] )
      pad_track_db_session ();

    if ( $GLOBALS['pad_track_file'] )
      pad_track_file ();

    pad_close_session ();
    pad_empty_buffers ();

    if ( ! isset($GLOBALS['pad_sent']) ) {

      $GLOBALS['pad_sent'] = TRUE;

      pad_headers ();
    
      if ( $stop == 200 )
        echo $GLOBALS['pad_output'];

    }  

    pad_exit ();

  }


  function pad_headers () {

    $stop = $GLOBALS['pad_stop'];
    $len  = $GLOBALS['pad_len'];
    $etag = $GLOBALS['pad_etag'];

    if ( $stop <> 302 and $stop <> 304)
      pad_header ('Content-Type: text/html; charset=UTF-8');
    
    if ( $stop == 500 )
      pad_header ('HTTP/1.0 500 Internal Server Error' );
    elseif ( $stop == 304 )
      pad_header ('HTTP/1.1 304 Not Modified');
      
    if ( $stop <> 200 and $stop <> 304 )

      pad_header ('Cache-Control: no-cache, no-store');

    else {

      if ( $GLOBALS ['pad_cache_proxy_age'] ) {
        $type = 'public';
        $sage = $GLOBALS ['pad_cache_proxy_age'] - ($_SERVER['REQUEST_TIME'] - $time);
        if ($sage < 0)
          $sage = 0;
        $sage = "$sage, proxy-revalidate";
      } else {
        $type = 'private';
        $sage = '0';
      }
      
      $age = ( $GLOBALS ['pad_cache_client_age'] ) ? $GLOBALS ['pad_cache_client_age'] - ($_SERVER['REQUEST_TIME'] - $time) : 0 ;
      if ($age < 0)
        $age = 0;
      
      pad_header ('Etag: '          . "\"$etag\"");
      pad_header ('Cache-Control: ' . "$type, max-age=$age, must-revalidate, s-maxage=$sage");
      pad_header ('Vary: '          . 'Content-Encoding');
      pad_header ('Date: '          . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME']) . ' GMT');;

    } 

    pad_timing_close ();

    if ( $stop == 200 ) {
      if ( $GLOBALS ['pad_client_gzip'] )
        pad_header ('Content-Encoding: gzip');
      pad_header ('Content-Length: ' . $len);
    }

  }


  function pad_exit () {

    $GLOBALS['pad_skip_shutdown']      = TRUE;
    $GLOBALS['pad_skip_boot_shutdown'] = TRUE;

    if ( $GLOBALS['pad_trace'] )
      include PAD_HOME . 'exits/trace.php';
    
    exit;

  }


?>