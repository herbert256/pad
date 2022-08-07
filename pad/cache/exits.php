<?php
  
  pTiming_start ('cache');
  
  if ( $pEtag == $pCache_etag )
  
    pCache_update ($pCache_url, $pEtag);

  else {

    if ($pCache_etag)
      pCache_delete ($pCache_url, $pCache_etag);

    if ( $pCache_server_gzip and ! $pClient_gzip )
      pCache_store ($pCache_url, $pEtag, pZip($pOutput));
    else
      pCache_store ($pCache_url, $pEtag, $pOutput);
    
  }

  if ( $pTrace_cache ) {
    $pCache_stop = $pStop + .4;
    include 'trace.php';
  }
  
  pTiming_end ('cache');
    
?>