<?php

  $pCache_trace = [
    'stop'        => $pCache_stop,
    'url'         => $pCache_url,
    'now-etag'    => $pEtag,
    'cache-etag'  => $pCache_etag,
    'client-etag' => $pCache_client,
    'age'         => $pCache_age,
    'mod'         => $pCache_mod,
    'max'         => $pCache_max,
    'now-age'     => ($pCache_age) ? $_SERVER['REQUEST_TIME'] - $pCache_age : 0,
    'now-mod'     => ($pCache_mod) ? $_SERVER['REQUEST_TIME'] - $pCache_mod : 0,
    'now-max'     => ($pCache_max) ? $_SERVER['REQUEST_TIME'] - $pCache_max : 0
  ];
      
  pFile_put_contents ( $pTrace_dir . "/cache.$pCache_stop.json", $pCache_trace );

?>