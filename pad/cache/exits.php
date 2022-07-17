<?php
  
  pad_timing_start ('cache');
  
  if ( $pad_etag == $pad_cache_etag )
  
    pad_cache_update ($pad_cache_url, $pad_etag);

  else {

    if ($pad_cache_etag)
      pad_cache_delete ($pad_cache_url, $pad_cache_etag);

    if ( $pad_cache_server_gzip and ! $pad_client_gzip )
      pad_cache_store ($pad_cache_url, $pad_etag, pad_zip($pad_output));
    else
      pad_cache_store ($pad_cache_url, $pad_etag, $pad_output);
    
  }

  if ( $pad_trace_cache ) {
    $pad_cache_stop = $pad_stop + .4;
    include 'trace.php';
  }
  
  pad_timing_end ('cache');
    
?>