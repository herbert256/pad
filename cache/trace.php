<?php

  $padCacheTrace = [
    'stop'        => $padCacheStop,
    'url'         => $padCacheUrl,
    'now-etag'    => $padEtag,
    'cache-etag'  => $padCacheEtag,
    'client-etag' => $padCacheClient,
    'age'         => $padCacheAge,
    'mod'         => $padCacheMod,
    'max'         => $padCacheMax,
    'now-age'     => ($padCacheAge) ? $_SERVER['REQUEST_TIME'] - $padCacheAge : 0,
    'now-mod'     => ($padCacheMod) ? $_SERVER['REQUEST_TIME'] - $padCacheMod : 0,
    'now-max'     => ($padCacheMax) ? $_SERVER['REQUEST_TIME'] - $padCacheMax : 0
  ];
      
  padFilePutContents ( $padTraceDir . "/cache.$padCacheStop.json", $padCacheTrace );

?>