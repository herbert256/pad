<?php

  $pad_cache_trace = [
    'stop'        => $pad_cache_stop,
    'url'         => $pad_cache_url,
    'now-etag'    => $pad_etag,
    'cache-etag'  => $pad_cache_etag,
    'client-etag' => $pad_cache_client,
    'age'         => $pad_cache_age,
    'mod'         => $pad_cache_mod,
    'max'         => $pad_cache_max,
    'now-age'     => ($pad_cache_age) ? $_SERVER['REQUEST_TIME'] - $pad_cache_age : 0,
    'now-mod'     => ($pad_cache_mod) ? $_SERVER['REQUEST_TIME'] - $pad_cache_mod : 0,
    'now-max'     => ($pad_cache_max) ? $_SERVER['REQUEST_TIME'] - $pad_cache_max : 0
  ];
      
  pad_file_put_contents ( $pad_trace_dir . "/cache.$pad_cache_stop.json", $pad_cache_trace );

?>