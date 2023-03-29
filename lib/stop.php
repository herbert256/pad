<?php

 
  function padStop ($stop) {

    $GLOBALS ['padStop'] = $stop;
    $GLOBALS ['padLen']  = ( $stop == 200 ) ? strlen($GLOBALS ['padOutput']) : 0;

    if ( $GLOBALS ['padTrackDbSession'] or $GLOBALS ['padTrackDbRequest'] )
      padTrackDbSession ();

    if ( $GLOBALS ['padTrackFileRequest'] )
      padTrackFileRequest ();

    padCloseSession ();
    padEmptyBuffers ();

    if ( ! isset($GLOBALS ['padSent']) )
      padSend ($stop);

    padExit ();

  }


  function padSend ($stop) {  

    padHeaders ($stop);

    if ( ! $GLOBALS ['padOutput'] )
      return;

    if ( $stop == 200 ) {

      if ( $GLOBALS ['padCacheStop'] and $GLOBALS ['padCacheServerGzip'] and $GLOBALS ['padClientGzip']  )
        echo $GLOBALS ['padOutput'];
      elseif ( $GLOBALS ['padCacheStop'] and $GLOBALS ['padCacheServerGzip'] and ! $GLOBALS ['padClientGzip'] )
        echo padUnzip ( $GLOBALS ['padOutput'] );
      elseif ( $GLOBALS ['padClientGzip'] )
        echo padZip ( $GLOBALS ['padOutput'] );
      else 
        echo $GLOBALS ['padOutput'];

    }

    $GLOBALS ['padSent'] = TRUE;

  }


  function padHeaders ($stop) {

    if ( headers_sent () )
      return;

    padHeader ('X-PAD: ' . $GLOBALS ['PADSESSID'] . '-' . $GLOBALS ['PADSESSID']);

    if ( $stop == 500 )
      padHeader ('HTTP/1.0 500 Internal Server Error' );
    elseif ( $stop == 304 )
      padHeader ('HTTP/1.1 304 Not Modified');
    elseif ( $stop == 200 and $GLOBALS ['padClientGzip'] )
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


  function padExit () {

    $GLOBALS ['padSkipShutdown']     = TRUE;
    $GLOBALS ['padSkipBootShutdown'] = TRUE;

    include PAD . 'exits/trace.php';
    
    exit;

  }


  function padCloseSession () {

    if ( ! isset($GLOBALS ['padSessionStarted']) )
      return;

    foreach ( $GLOBALS ['padSessionVars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }

  
?>