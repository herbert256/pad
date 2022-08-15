<?php
  
  pTiming_start ('cache');
  
  if ( $padEtag == $padCache_etag )
  
    pCache_update ($padCache_url, $padEtag);

  else {

    if ($padCache_etag)
      pCache_delete ($padCache_url, $padCache_etag);

    if ( $padCache_server_gzip and ! $padClient_gzip )
      pCache_store ($padCache_url, $padEtag, pZip($padOutput));
    else
      pCache_store ($padCache_url, $padEtag, $padOutput);
    
  }

  if ( $padTrace ) {
    $padCache_stop = $padStop + .4;
    include 'trace.php';
  }
  
  pTiming_end ('cache');
    
?>