<?php
  
  padTimingStart ('cache');
  
  if ( $padEtag == $padCache_etag )
  
    padCacheUpdate ($padCache_url, $padEtag);

  else {

    if ($padCache_etag)
      padCacheDelete ($padCache_url, $padCache_etag);

    if ( $padCache_server_gzip and ! $padClient_gzip )
      padCacheStore ($padCache_url, $padEtag, padZip($padOutput));
    else
      padCacheStore ($padCache_url, $padEtag, $padOutput);
    
  }

  if ( $padTrace ) {
    $padCache_stop = $padStop + .4;
    include 'trace.php';
  }
  
  padTimingEnd ('cache');
    
?>