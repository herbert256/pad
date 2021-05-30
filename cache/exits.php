<?php
  
  pad_timing_start ('cache');
  
  if ( $pad_etag == $pad_cache_etag )
  
    "pad_cache_update_$pad_cache_server_type" ($pad_cache_url, $pad_etag);

  else {

    if ( $pad_cache_server_gzip and ! $pad_client_gzip )
      "pad_cache_store_$pad_cache_server_type" ($pad_cache_url, $pad_etag, pad_zip($pad_output));
    else
      "pad_cache_store_$pad_cache_server_type" ($pad_cache_url, $pad_etag, $pad_output);

    if ($pad_cache_etag)
      "pad_cache_delete_$pad_cache_server_type" ($pad_cache_url, $pad_cache_etag);
    
  }

  pad_timing_end ('cache');
    
?>