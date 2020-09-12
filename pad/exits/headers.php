<?php


    if ( $pad_stop <> 302 and $pad_stop <> 304)
      pad_header ('Content-Type: text/html; charset=UTF-8');
    
    if ($pad_stop==500)
      pad_header ('HTTP/1.0 500 Internal Server Error' );
    elseif ($pad_stop==304)
      pad_header ('HTTP/1.1 304 Not Modified');
    elseif ($pad_stop<>302) {
      if ( $pad_client_gzip )
        pad_header ('Content-Encoding: gzip');
      pad_header ('Content-Length: ' . $pad_len);
    }
     
    if ( $pad_stop<>200 and $pad_stop<>304 )
 
      pad_header ('Cache-Control: no-cache, no-store');
 
    else {

      if ($GLOBALS ['pad_cache_proxy_age'] ) {
        $type = 'public';
        $sage = $GLOBALS ['pad_cache_proxy_age'] - ($_SERVER['REQUEST_TIME'] - $pad_time);
        if ($sage < 0)
          $sage = 0;
        $sage = "$sage, proxy-revalidate";
      } else {
        $type = 'private';
        $sage = '0';
      }
      
      $age = ( $GLOBALS ['pad_cache_client_age'] ) ? $GLOBALS ['pad_cache_client_age'] - ($_SERVER['REQUEST_TIME'] - $pad_time) : 0 ;
      if ($age < 0)
        $age = 0;
      
      pad_header ('Etag: '          . "\"$pad_etag\"");
      pad_header ('Cache-Control: ' . "$type, max-age=$age, must-revalidate, s-maxage=$sage");
      pad_header ('Vary: '          . 'Content-Encoding');
      pad_header ('Date: '          . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME']) . ' GMT');;

    } 
  
    pad_timing_close ();

?>