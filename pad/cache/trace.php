<?php

  $padCache_trace = [
    'stop'        => $padCache_stop,
    'url'         => $padCache_url,
    'now-etag'    => $padEtag,
    'cache-etag'  => $padCache_etag,
    'client-etag' => $padCache_client,
    'age'         => $padCache_age,
    'mod'         => $padCache_mod,
    'max'         => $padCache_max,
    'now-age'     => ($padCache_age) ? $_SERVER['REQUEST_TIME'] - $padCache_age : 0,
    'now-mod'     => ($padCache_mod) ? $_SERVER['REQUEST_TIME'] - $padCache_mod : 0,
    'now-max'     => ($padCache_max) ? $_SERVER['REQUEST_TIME'] - $padCache_max : 0
  ];
      
  padFilePutContents ( $padTraceDir . "/cache.$padCache_stop.json", $padCache_trace );

?>